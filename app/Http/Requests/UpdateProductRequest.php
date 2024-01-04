<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'  => '|max:50|min:2',
            'price' => '|numeric',
            'image' => '|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'color' => '',
            'description' => '|max:500|min:2',
            'ram' => 'integer|numeric|digits_between:1,2',
            'storage'=>'integer',
            'buy'=>'integer',
            'stock'=>'integer',
            'action' => 'integer|digits:1'
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
