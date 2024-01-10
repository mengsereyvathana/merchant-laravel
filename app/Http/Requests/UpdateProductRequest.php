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
            // 'name'  => 'required|max:50|min:2',
            // 'price' => 'required|numeric',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'color' => 'required|',
            // 'description' => 'required|max:500|min:2',
            // 'ram' => 'required|numeric|digits_between:1,2',
            // 'storage' => 'required',
            // 'buy' => 'required',
            // 'stock' => 'required',
            // 'category_id' => 'required',
            // 'action' => 'required'
        ];
    }

    /* for response message when the process error */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation error',
            'data'      => $validator->errors()
        ], 400));
    }
}
