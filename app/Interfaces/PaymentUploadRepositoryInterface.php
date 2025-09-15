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
}
