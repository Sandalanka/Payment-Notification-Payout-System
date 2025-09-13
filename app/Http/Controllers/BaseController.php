<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * Summary: send success response
     * 
     * @param  mixed $result
     * @param  mixed $message
     * @return void
     */
    public function sendResponse($result , $message, $code=200){
        $response=[
            'success' => true,
            'data'    => $result
        ];
        if(!empty($message)){
            $response['message'] =$message;
        }
        return response()->json($response, $code);
    }
    
    /**
     * Summary: send error response
     * 
     * @param  mixed $error
     * @return void
     */
    public function sendError($error, $code=400)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];

        return response()->json($response, $code);
    }
}
