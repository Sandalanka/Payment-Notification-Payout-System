<?php

namespace App\Repositories;

use App\Interfaces\PaymentUploadRepositoryInterface;
use App\Classes\ApiCatchErrors;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PaymentImport;

class PaymentUploadRepository implements PaymentUploadRepositoryInterface
{
    /**
     *
     * Summary:UploadExcel file
     *
     * @param UploadedFile $file
     * @return void
     */
    public function uploadExcel(UploadedFile $file): void
    {
        try {
            Excel::queueImport(new PaymentImport, $file);

        } catch (Exception $exception) {
            ApiCatchErrors::throw($exception);
        }
    }

    /**
     *
     * Summary: Upload S3 bucket
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string
     */
    public function uploadS3(UploadedFile $file, string $directory = 'payment-files'): string

    {
        try {
            $fileName = $directory . '/' . date('Y/m/d') . '/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('s3')->put($fileName, file_get_contents($file->getRealPath()));

            return $fileName;

        } catch (Exception $exception) {
            ApiCatchErrors::throw($exception);
        }
    }
}
