<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            "password_confirmation" => [
                'required_with:password',
                'same:password',
                'min:8',
            ]
        ];
    }

    public function messages()
    {
        return [
            "last_name.required" => "LAST_NAME_REQUIRED",
            "first_name.required" => "FIRST_NAME_REQUIRED",
            "email.required" => "EMAIL_REQUIRED",
            "email.email" => "EMAIL_MUST_BE_VALID_EMAIL_ADDRESS",
            "password.required" => "PASSWORD_REQUIRED",
            "password.min" => "PASSWORD_MIN_8_CHARACTERS",
            "password_confirmation.required_with" => "PASSWORD_CONFIRMATION_REQUIRED",
            "password_confirmation.min" => "PASSWORD_CONFIRMATION_MIN_8_CHARACTERS",
            "password_confirmation.same" => "PASSWORD_AND_CONFIRMATION_MUST_MATCH",
        ];
    }
}
