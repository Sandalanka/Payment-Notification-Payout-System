<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PaymentImport;
use App\Classes\ApiCatchErrors;

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
        try{
            $fullPath = storage_path("app/{$this->path}");
            Excel::import(new PaymentImport, $fullPath);
        }catch(\Exception $exception){
            return ApiCatchErrors::throw($exception);
        }
        
    }
}
