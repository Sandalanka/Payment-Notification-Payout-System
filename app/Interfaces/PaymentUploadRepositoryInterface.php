<?php

namespace App\Interfaces;
use Illuminate\Http\UploadedFile;

interface PaymentUploadRepositoryInterface
{    
    /**
     * Summary:uploadExcel file
     *
     * @param  mixed $data
     * @return void
     */
    public function uploadExcel(UploadedFile $file);
    
    /**
     * Summary:upload aws s3 bucket
     *
     * @param  mixed $file
     * @return void
     */
    public function uploadS3(UploadedFile $file, string $directory);
}

