<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
class VerifyEmailOtpRequest extends FormRequest
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

        if(is_numeric($this->get('emailPhone'))){
            $emailPhone     = 'numeric|digits_between:8,10';
            // |regex:/^0[1-9][0-9]*/
        }
        else{           
            $emailPhone     = 'email|max:50';      
        } 
        // return $rules;
        // |exists:users,email
        return [
            'emailPhone'    => $emailPhone,
            'otp'      => 'required|numeric|digits:4'
        ];     
    }
    
    // unique:User,email|
    public function messages()
    {
        return [
            // 'emailPhone.regex'              => 'The number must start with 0 and after must be different from 0',
            'emailPhone.digits_between'     => 'The phone field must be between 8 and 10 digits.',
            'otp.digits'                    => 'The otp code field must be between 4 digits.',
            'emailPhone.email'              => 'Invalid email address.',
            'emailPhone.max'                => 'The email field must not be greater than 50 characters.',

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
