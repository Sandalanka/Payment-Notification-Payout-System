<?php

namespace App\Classes;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiCatchErrors
{     
     /**
      * 
      * Summary: rollback db transaction failure somthing
      * @param  mixed $e
      * @param  mixed $message
      * @return void
      */
     public static function rollback($errors, $message ="Something went wrong! Process not completed"){
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
    public static function throw($error, $message ="Something went wrong! Process not completed"){
        Log::error('Error message: ' . $error->getMessage());
        throw new HttpResponseException(response()->json(["message"=> $error->getMessage()], 500));
    }
    
}
