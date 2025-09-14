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
        $amountUsd = $this->convertToUsd($amount, $origCurrency);

        $payment = Payment::create([
                    'customer_id' => $row['customer_id'],
                    'customer_name' => $row['customer_name'],
                    'customer_email' => $row['customer_email'],
                    'original_amount' => $row['amount'],
                    'original_currency' => $row['currency'],
                    'amount_usd' => $amountUsd,
                    'processed_at' => $row['date_time'],
                    'reference_no' => $row['reference_no'],
                    'processed' => false,
                    // 'uploaded_by' => $row['name'],
               ]);

        PaymentLog::create([
            'row_number' => $payment->id,
            'status'     => 'success',
            'raw_data'   => json_encode($row),
        ]);
    }
     
     /**
      * Summary:excel row data validation rules
      *
      * @return array
      */
    public function rules(): array
    {
        return [
            'customer_id'    => 'required',
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email',
            'amount'         => 'required|numeric|min:0',
            'currency'       => 'required',
            'date_time'      => 'required'
        ];
    }
    
    /**
     * Summary: vlidation fail data
     *
     * @param  mixed $failures
     * @return void
     */
    public function onFailure(...$failures)
    {
        foreach ($failures as $failure) {
            Log::error('Import failed', [
                'row'      => $failure->row(),
                'attribute'=> $failure->attribute(), 
                'errors'   => $failure->errors(),
                'values'   => $failure->values(), 
            ]);
             
            PaymentLog::create([
                'row_number' => $failure->row(),
                'status'     => 'failure',
                'message'    => json_encode($failure->errors()),
                'raw_data'   => json_encode($failure->values()),
            ]);
            
        }
    }

    
    /**
     * Summary: getRates usd to other currency
     *
     * @return void
     */
    public function getRates()
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            $response = Http::withHeaders([
                'apikey' => env('CURRENCY_API_KEY')
            ])->get('https://api.apilayer.com/currency_data/live?source=USD');

            if ($response->successful()) {
                return $response->json()['quotes'];
            }

            return []; 
        });
    }

    /**
     * Summary: Convert given amount from a currency to USD.
     */
    public function convertToUSD($amount, $currency)
    {
        $rates = $this->getRates();

        $key = 'USD' . strtoupper($currency);

        if (!isset($rates[$key])) {
            throw new \Exception("Currency rate for {$currency} not found.");
        }

        return $amount / $rates[$key];
    }

}
