<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\PaymentLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Throwable;
use Log;
class ProcessPaymentFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // public string $filePa 
    public $path;

    // optionally set attempts/timeouts
    public $tries = 3;
    public $timeout = 120;

    public function __construct(string $path)
    {
         $this->path = $path;
    }

    public function handle()
    {
         Log::info(1);
        $fullPath = storage_path("app/{$this->path}");
      Log::info(2);
        Excel::import(new class implements \Maatwebsite\Excel\Concerns\ToCollection {
            use \Illuminate\Queue\SerializesModels;

            public function collection(\Illuminate\Support\Collection $rows)
            {
                Log::info(3);
                $rows->skip(1)->each(function ($row) {
                    $data = [
                        'customer_id'    => $row[0],
                        'customer_name'  => $row[1],
                        'customer_email' => $row[2],
                        'amount'         => $row[3],
                        'currency'       => strtoupper($row[4]),
                        'processed_at'   => $row[6],
                        'reference_no'   => $row[7],
                    ];
                    Log::info(4);
                    // Convert to USD dynamically
                    $data['amount_usd'] = $this->convertToUsd($data['amount'], $data['currency']);

                    $validator = Validator::make($data, [
                        'customer_id'    => 'required|integer',
                        'customer_name'  => 'required|string|max:255',
                        'customer_email' => 'required|email',
                        // 'amount'         => 'required|numeric|min:0',
                        // 'currency'       => 'required|string|max:10',
                        //'amount_usd'     => 'required|numeric|min:0',
                        //'processed_at'   => 'nullable|date',
                       // 'reference_no'   => 'required|string|max:255|unique:payments,reference_no',
                    ]);
                    Log::info(5);
                    if ($validator->fails()) {
                        Log::info(6);
                        // PaymentFailure::create([
                        //     'row_data' => $data,
                        //     'error_message' => implode('; ', $validator->errors()->all()),
                        // ]);
                    } else {
                        Log::info(7);
                        Payment::create($data);
                    }
                });
            }

            private function convertToUsd(float $amount, string $currency): float
            {
                try {
                    if ($currency === 'USD') {
                        return $amount;
                    }
Log::info(8);
                    $resp = Http::get('https://api.exchangerate.host/latest', [
                        'base' => $currency,
                        'symbols' => 'USD'
                    ]);
Log::info(8);
                    if ($resp->successful() && isset($resp['rates']['USD'])) {
                        return $amount * $resp['rates']['USD'];
                    }

                    return 0;
                } catch (\Throwable $e) {
                    Log::info($e);
                    return 0;
                }
            }
        }, $fullPath);
    }

    
}
