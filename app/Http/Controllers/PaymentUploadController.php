<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentUploadRequest;
use App\Jobs\ProcessPaymentFileJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Classes\ApiCatchErrors;
use App\Http\Controllers\BaseController as BaseController;

class PaymentUploadController extends BaseController
{    
    /**
     * Summary: upload excel and queue start
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function upload(PaymentUploadRequest $request): JsonResponse
    {   
        try{
            $path = $request->file('file')->store('payment_uploads');
            ProcessPaymentFileJob::dispatch($path);
            return $this->sendResponse("","File uploaded successfully and processing started.",201);
        }catch(\Exception $exception){
            return ApiCatchErrors::throw($exception);
        }
       
    }
}
