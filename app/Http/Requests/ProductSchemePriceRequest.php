<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Models\User;
use Illuminate\Validation\Rule;
class ProductSchemePriceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pro_id'=>'required|numeric',
            'scheme_id'=>'required|numeric',
            'unit_price'=>'required|numeric',
            // email|unique:users
        ];
    }
    public function failedValidation(Validator $validator)
    {
       
       throw new HttpResponseException(response()->json([
         'success'   => false,
         'message'   => 'Validation errors',
         'data'      => $validator->errors()
       ],200));
    }
}