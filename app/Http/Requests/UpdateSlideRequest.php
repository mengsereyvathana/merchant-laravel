<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateSlideRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'new_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:3048',
            'new_order'=>'numeric|regex:/^(?!0+$)[0-9]+$/',
        ];
    }

    /* for response message when the process error */
    public function failedValidation(Validator $validator)
{
   throw new HttpResponseException(response()->json([
     'success'   => false,
     'message'   => 'Validation errors',
     'data'      => $validator->errors()
   ],400));
}
}
