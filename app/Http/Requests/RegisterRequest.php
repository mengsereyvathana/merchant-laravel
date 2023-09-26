<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Models\User;
use Illuminate\Validation\Rule;
class RegisterRequest extends FormRequest
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

        if(is_numeric($this->get('PhoneEmail'))){
            $PhoneEmail     = 'numeric|unique:users,phone|digits_between:8,10';
            $unique_message = 'The phone number has already been taken.';
            // |regex:/^0[1-9][0-9]*/
        }
        else{           
            $PhoneEmail     = 'email|unique:users,email|max:50';
            // $PhoneEmail     = 'required';
            $unique_message = 'The email has already been taken.';       
        } 
        // return $rules;
        // |exists:users,email
        return [
            $unique_message,
            'PhoneEmail'    => $PhoneEmail,
            'password'      => 'required|numeric|digits_between:8,20',
            'password_confirm' => 'required|numeric|digits_between:8,20|same:password'
        ];     
    }
    
    // unique:User,email|
    public function messages()
    {
        $unique_message = $this->rules();
        return [
            'PhoneEmail.regex'              => 'The number must start with 0 and after must be different from 0',
            'PhoneEmail.digits_between'     => 'The phone field must be between 8 and 10 digits.',
            'PhoneEmail.email'              => 'Invalid email address.',
            'PhoneEmail.unique'             => $unique_message[0],
            'PhoneEmail.max'                => 'The email field must not be greater than 50 characters.',

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
