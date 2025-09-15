<?php

namespace App\Repositories;
use App\Interfaces\PaymentUploadRepositoryInterface;
use App\Jobs\ProcessPaymentFileJob;
use App\Classes\ApiCatchErrors;
use Illuminate\Http\UploadedFile;

class PaymentUploadRepository implements  PaymentUploadRepositoryInterface
{
    /**
     * Summary:uploadExcel file
     *
     * @param  mixed $data
     * @return void
     */
    public function uploadExcel(UploadedFile $file)
    {
        try{
            $path = $file->store('payment_uploads');
            ProcessPaymentFileJob::dispatch($path);
        }catch(\Exception $exception){
            return ApiCatchErrors::throw($exception);
         }
    }

}
