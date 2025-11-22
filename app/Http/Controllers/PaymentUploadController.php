<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentUploadRequest;
use Illuminate\Http\JsonResponse;
use App\Classes\ApiCatchErrors;
use App\Interfaces\PaymentUploadRepositoryInterface;

class PaymentUploadController extends Controller
{
    private PaymentUploadRepositoryInterface $paymentUploadRepositoryInterface;

    /**
     *
     *Summery:  __construct
     *
     * @param mixed $paymentUploadRepositoryInterface
     */
    public function __construct(PaymentUploadRepositoryInterface $paymentUploadRepositoryInterface)
    {
        $this->paymentUploadRepositoryInterface = $paymentUploadRepositoryInterface;
    }

    /**
     *
     * Summary: Upload excel and queue start
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function upload(PaymentUploadRequest $request): JsonResponse
    {
        try{
            $fileName = $this->paymentUploadRepositoryInterface->uploadS3($request->file('file'));
            
            $this->paymentUploadRepositoryInterface->uploadExcel($request->file('file'));

            return $this->sendResponse(message: "File uploaded successfully and processing started.",
                                       statusCode: 201);

        }catch(\Exception $exception){
            ApiCatchErrors::throw($exception);
        }
    }
}
