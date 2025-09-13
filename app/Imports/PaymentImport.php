<?php

namespace App\Imports;

use App\Models\Payment;
use App\Models\PaymentLog;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PaymentImport implements ToModel, SkipsOnFailure, WithHeadingRow ,WithValidation
{
    use SkipsFailures;
    protected $cacheKey = 'currency_rates';
    protected $cacheTtl = 3600;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {   
        $origCurrency = strtoupper($row['currency'] ?? 'USD');
        $amount = floatval($row['amount']);
        // $rates = $this->getRates();
        $amountUsd = $this->convertToUsd($amount, $origCurrency);
Log::info($amountUsd);
// Log::info($rates);
        Payment::create([
            'customer_id' => $row['customer_id'],
            'customer_name' => $row['customer_name'],
            'customer_email' => $row['customer_email'],
            'original_amount' => $row['amount'],
            'original_currency' => $row['currency'],
            'amount_usd' => $amountUsd,
            'processed_at' => $row['date_time'],
            'reference_no' => $row['reference_no'],
            'processed' => true,
            // 'uploaded_by' => $row['name'],
            // 'uploaded_file' => $row['name']
        ]);
    }

     public function rules(): array
    {
        return [
            'customer_id'    => 'required',
            'customer_name'  => 'required|string|max:255',
            //'customer_email' => 'required|email',
            'amount'         => 'required|numeric|min:0',
            'currency'       => 'required|string|max:10',
            'date_time'      => 'required'
        ];
    }
 public function onFailure(...$failures)
    {
        foreach ($failures as $failure) {
            // Log into storage/logs/laravel.log
            Log::error('Import failed', [
                'row'      => $failure->row(), // row index
                'attribute'=> $failure->attribute(), // column name
                'errors'   => $failure->errors(), // validation error messages
                'values'   => $failure->values(), // actual row values
            ]);
             
            PaymentLog::create([
            'row_number' => $failure->row(),
            'status'     => 'failure',
            'message'    => json_encode($failure->errors()), // store as JSON
            'raw_data'   => json_encode($failure->values()), // store as JSON
        ]);
            
        }
    }

    

    protected function convertToUsdddd(float $amount, string $currency, array $rates): float
    {
        $currency = strtoupper($currency);
        if ($currency === 'USD') return round($amount, 4);

        // exchangerate.host returns rates as USD base => target values,
        // but because we used base=USD, rates[TARGET] = target per 1 USD.
        // To convert from currency -> USD: amount_in_usd = amount / rate_of_currency
        if (isset($rates[$currency]) && floatval($rates[$currency]) > 0) {
            $rate = floatval($rates[$currency]);
            $usd = $amount / $rate;
            return round($usd, 4);
        }

        // fallback: attempt API again for specific conversion
        try {
            // $resp = Http::get('https://api.exchangerate.host/convert', [
            //     'from' => $currency,
            //     'to' => 'USD',
            //     'amount' => $amount,
            // ]);
//            $resp= Http::get('https://api.example.com/convert', [
//     'access_key' => env('CURRENCY_API_KEY'),
//     'from' => $currency,
//     'to' => 'USD',
//     'amount' => $amount,
// ]);   
// $resp = Http::withHeaders([
//     'Content-Type' => 'text/plain',
//     'apikey' => 'QNbQWFAEDjvLSh8hTJCecLgQUIPJh7WE',
// ])->get('https://api.apilayer.com/currency_data/live', [
//     'source' => 'USD',       // replace with your source currency
//     // 'currencies' => 'USD' // replace with target currencies, comma-separated
// ]);
            Log::info($resp);
            if ($resp->ok()) {
                $data = $resp->json();
                return round(floatval($data['result'] ?? 0), 4);
            }
        } catch (Throwable $e) {
            Log::info($e);
            // ignore
        }

        return 0.0;
    }

    public function getRates()
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            $response = Http::withHeaders([
                'apikey' => env('CURRENCY_API_KEY')
            ])->get('https://api.apilayer.com/currency_data/live?source=USD');

            if ($response->successful()) {
                return $response->json()['quotes'];
            }

            return []; // fallback empty array if API fails
        });
    }

    /**
     * Convert given amount from a currency to USD.
     */
    public function convertToUSD($amount, $currency)
    {
        $rates = $this->getRates();

        $key = 'USD' . strtoupper($currency);

        if (!isset($rates[$key])) {
            throw new \Exception("Currency rate for {$currency} not found.");
        }

        // Conversion: amount / USD to currency rate
        return $amount / $rates[$key];
    }
}
