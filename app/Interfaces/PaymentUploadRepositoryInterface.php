<?php

namespace App\Interfaces;

use Illuminate\Http\UploadedFile;

interface PaymentUploadRepositoryInterface
{
    /**
     * Summary: UploadExcel file
     *
     * @param UploadedFile $file
     * @return void
     */
    public function uploadExcel(UploadedFile $file);

    /**
     * Summary: Upload aws s3 bucket
     *
     * @param mixed $file
     * @param string $directory
     * @return string
     */
    public function uploadS3(UploadedFile $file, string $directory): string;
}

