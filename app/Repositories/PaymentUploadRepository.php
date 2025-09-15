<?php

namespace App\Repositories;
use App\Interfaces\PaymentUploadRepositoryInterface;
use App\Jobs\ProcessPaymentFileJob;
use App\Classes\ApiCatchErrors;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PaymentImport;

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
             Excel::queueImport(new PaymentImport, $file);
          
        }catch(\Exception $exception){
            return ApiCatchErrors::throw($exception);
         }
    }

    public function uploadS3(UploadedFile $file, string $directory = 'payment-files'){
        try{
            $fileName = $directory.'/'.date('Y/m/d').'/'.Str::uuid().'.'.$file->getClientOriginalExtension();
            Storage::disk('s3')->put($fileName, file_get_contents($file->getRealPath()));
            return $fileName;

        }catch(\Exception $exception){
            return ApiCatchErrors::throw($exception);
         }
        
    }

}
