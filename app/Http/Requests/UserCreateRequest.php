<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
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
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'EMAIL_REQUIRED',
            'email.email' => 'EMAIL_MUST_BE_A_VALID_EMAIL',
            'first_name.required' => 'FIRST_NAME_REQUIRED',
            'last_name.required' => 'LAST_NAME_REQUIRED',
        ];
    }
}
