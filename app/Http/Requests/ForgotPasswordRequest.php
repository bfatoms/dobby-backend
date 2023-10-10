<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
            'browser' => 'required',
            'operating_system' => 'required',
            'email' => 'required|email'
        ];
    }

    public function messages()
    {
        return [
            "browser.required" => "BROWSER_REQUIRED",
            "operating_system.required" => "OPERATING_SYSTEM_REQUIRED",
            "email.required" => "EMAIL_REQUIRED",
            "email.email" => "EMAIL_MUST_BE_VALID_EMAIL_ADDRESS",
        ];
    }
}
