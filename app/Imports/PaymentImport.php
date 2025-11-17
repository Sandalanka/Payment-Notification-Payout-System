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
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class PaymentImport implements ToModel, SkipsOnFailure, WithHeadingRow ,WithValidation, WithChunkReading, ShouldQueue
{
    use SkipsFailures;
    protected $cacheKey = 'currency_rates';
    protected $cacheTtl = 3600;
    
        
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Set the formatter to 'none' so array keys match the exact heading row data
        HeadingRowFormatter::default('none');
    }

    /**
     * Summary:chunkSize define
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 500; 
    }
    
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
                    'uploaded_by' => Auth::id()
               ]);

        PaymentLog::create([
            'paymnet_id' => $payment->id,
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
            // 'customer_id'    => '',
            'customer_name'  => 'string|max:255',
            'customer_email' => 'email',
            'amount'         => 'numeric|min:0',
            // 'currency'       => '',
            // 'date_time'      => ''
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
        $allErrors = [];

        foreach ($failures as $failure) {
            $allErrors = array_merge($allErrors, $failure->errors());
        }

        Log::error($failure);
        PaymentLog::create([
            'row_number' => null,
            'status'     => 'failure',
            'message'    => json_encode($allErrors),
            'raw_data'   => json_encode($failures[0]->values()), 
        ]);
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
