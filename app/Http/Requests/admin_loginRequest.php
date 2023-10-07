<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class admin_loginRequest extends FormRequest
{
    public function rules(): array
    {

        // return $rules;
        // |exists:users,email
        return [
            'name'    => 'required',
            'password'      => 'required|numeric|digits_between:8,20'
        ];
    }

    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ], 200));
    }
}
