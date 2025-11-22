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
     *
     * @param $errors
     * @param null|string $message
     * @return void
     */
    public static function rollback($errors, null|string $message = Message::GENERAL_API_CATCH_ERROR_MESSAGE): void
    {
        DB::rollBack();
        self::throw($errors, $message);
    }

    /**
     *
     * Summary: throw error return
     *
     * @param $error
     * @param null|string $message
     * @return void
     */
    public static function throw($error, null|string $message = Message::GENERAL_API_CATCH_ERROR_MESSAGE): void
    {
        Log::error('Error message: ' . $error->getMessage());

        throw new HttpResponseException(response()->json(["message" => $error->getMessage()], 500, [
                'Access-Control-Allow-Origin' => '*',
                'Content-Type' => 'application/json'
            ]
        ));
    }
}
