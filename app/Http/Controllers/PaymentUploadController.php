<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentUploadRequest;
use App\Jobs\ProcessPaymentFileJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Classes\ApiCatchErrors;
use App\Http\Controllers\BaseController as BaseController;
use App\Interfaces\PaymentUploadRepositoryInterface;

class PaymentUploadController extends BaseController
{    
    
    private PaymentUploadRepositoryInterface $aymentUploadRepositoryInterface;
    
        
    /**
     * __construct
     *
     * @param  mixed $paymentUploadRepositoryInterface
     * @return void
     */
    public function __construct(PaymentUploadRepositoryInterface $paymentUploadRepositoryInterface)
    {
        $this->paymentUploadRepositoryInterface = $paymentUploadRepositoryInterface;
    }
    
    /**
     * Summary: upload excel and queue start
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function upload(PaymentUploadRequest $request): JsonResponse
    {   
        try{
           // $fileName = $this->paymentUploadRepositoryInterface->uploadS3($request->file('file'));
            $this->paymentUploadRepositoryInterface->uploadExcel($request->file('file'));
            return $this->sendResponse("","File uploaded successfully and processing started.",201);
        }catch(\Exception $exception){
            return ApiCatchErrors::throw($exception);
        }
       
    }
}
