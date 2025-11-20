<?php

namespace App\Http\Controllers;

use App\Constant\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Exception;
use Illuminate\Support\Collection;

abstract class Controller
{
    /**
     * Summary: send success response
     * 
     * @param  mixed $result
     * @param  mixed $message
     * @return void
     */
    public function sendResponse( array $result = null , 
                                  string $message = Message::GENERAL_RESPONSE_SUCCESS_MESSAGE, 
                                  int $statusCode = 200
                                ): JsonResponse
    {
        $response=[
            'status' => 'Success',
            'timestamp' => now()->toDateTimeString()
        ];

       if ($message !== null) {
            $response['message'] = $message;
        }

        if (!empty($data)) {
            $response['data'] = $data;
        }
        
        return response()->json($response, $statusCode, [
                'Access-Control-Allow-Origin' => '*',
                'Content-Type' => 'application/json'
            ]
        );
    }
    
     /**
     *
     * Summary: Return an error JSON response.
     *
     * @param Exception|null $exception
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function errorResponse(Exception $exception = null,
                                     string    $message = Messages::GENERAL_RESPONSE_ERROR_MESSAGE,
                                     int       $statusCode = 500): JsonResponse
    {
        $response = [
            'status' => 'failed',
            'message' => $message,
            'timestamp' => now()->toDateTimeString()
        ];

        if ($exception !== null) {
            $response['errors'] = $exception->getMessage();
        }

        throw new HttpResponseException(response()->json($response, $statusCode, [
                'Access-Control-Allow-Origin' => '*',
                'Content-Type' => 'application/json'
            ]
        ));
    }
}
