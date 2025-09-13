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
use App\Imports\PaymentImport;
use App\Classes\ApiCatchErrors;
use Illuminate\Support\Facades\DB; 

class ProcessPaymentFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $path;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(string $path)
    {
         $this->path = $path;
    }
    
    /**
     * Summary:job handle function
     *
     * @return void
     */
    public function handle()
    {   
        DB::beginTransaction();
        try{
            $fullPath = storage_path("app/{$this->path}");
            Excel::import(new PaymentImport, $fullPath);
            DB::commit();
        }catch(\Exception $exception){
            return ApiCatchErrors::rollback($exception);
        }
        
    }
}
