<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "first_name" => "required",
            "last_name" => "required",
            "email" => "required|email",
            "password" => "required|min:8",
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'EMAIL_REQUIRED',
            'email.email' => 'EMAIL_MUST_BE_VALID_EMAIL_ADDRESS',
            'password.required' => 'PASSWORD_REQUIRED',
            'password.min' => 'PASSWORD_MINIMUM_8_CHARACTERS',
            'last_name.required' => 'LAST_NAME_REQUIRED',
            'first_name.required' => 'FIRST_NAME_REQUIRED',
        ];
    }
}
