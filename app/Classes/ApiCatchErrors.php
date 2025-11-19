<?php

namespace App\Classes;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Constant\Message;

class ApiCatchErrors
{     
     /**
      * 
      * Summary: rollback db transaction failure somthing
      * @param  mixed $e
      * @param  mixed $message
      * @return void
      */
     public static function rollback($errors, $message = Message::GENERAL_API_CATCH_ERROR_MESSAGE){
        DB::rollBack();
        self::throw($errors, $message);
    }
    
    /**
     * 
     * Summary: throw error return 
     * @param  mixed $e
     * @param  mixed $message
     * @return void
     */
    public static function throw($error, $message = Message::GENERAL_API_CATCH_ERROR_MESSAGE){
        Log::error('Error message: ' . $error->getMessage());

        throw new HttpResponseException(response()->json(["message"=> $error->getMessage()], 500));
    }
    
}
