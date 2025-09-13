<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest as BaseRequest;

class RegisterRequest extends BaseRequest
{
    
    /**
     * Summary: Get user registrations the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required'
        ];
    }
}
