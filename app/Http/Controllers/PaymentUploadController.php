<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentUploadRequest;
use App\Jobs\ProcessPaymentFileJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\Imports\PaymentImport;

class PaymentUploadController extends Controller
{
    public function upload(PaymentUploadRequest $request): JsonResponse
    {
        Excel::import(new PaymentImport, $request->file('file'));
       // $file = $request->file('file');

        //$filename = 'payments/' . now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

        // Store to S3 (or configured disk)
       // $path = Storage::disk(config('filesystems.default'))->putFileAs('', $file, $filename);
// $path = $request->file('file')->store('payment_uploads');
        // dispatch job to process file asynchronously
       // ProcessPaymentFileJob::dispatch($path)->onQueue('payments');
//ProcessPaymentFileJob::dispatch($path);
        return response()->json([
            'message' => 'File uploaded successfully and processing started.',
           // 'path' => $path,
        ]);
    }
}
