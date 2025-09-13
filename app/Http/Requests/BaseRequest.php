<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BaseRequest extends FormRequest
{
    /**
     * Summary: Determine if the user is authorized to make this request.
     * 
     */
    public function authorize(): bool
    {
        return true;
    }
    
        
    /**
     * Summary: failedValidation.
     * 
     *
     * @param  mixed $validator
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
