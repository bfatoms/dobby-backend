<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
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
            "password" => "required|min:8",
            "password_confirmation" => 'required_with:password|same:password|min:8',
            "browser" => "required",
            "operating_system" => "required",
        ];
    }

    public function messages()
    {
        return [
            "password.required" => "PASSWORD_REQUIRED",
            "password.min" => "PASSWORD_MIN_8_CHARACTERS",
            "password_confirmation.required_with" => 'PASSWORD_CONFIRMATION_IS_REQUIRED_WITH_PASSWORD',
            "password_confirmation.same" => 'PASSWORD_AND_CONFIRMATION_MUST_MATCH',
            "password_confirmation.min" => 'PASSWORD_CONFIRMATION_MIN_8_CHARACTERS',
            "browser.required" => "BROWSER_REQUIRED",
            "operating_system.required" => "OPERATING_SYSTEM_REQUIRED",
        ];
    }
}
