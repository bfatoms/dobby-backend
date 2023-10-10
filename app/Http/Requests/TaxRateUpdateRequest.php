<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxRateUpdateRequest extends FormRequest
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
            'name' => 'required',
            'rate' => 'required|regex:/^\d+$/i|integer|max:99'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'NAME_REQUIRED',
            'rate.required' => 'RATE_REQUIRED',
            'rate.regex' => 'RATE_MUST_BE_POSITIVE_INTEGER',
            'rate.max' => 'RATE_MAX_99'
        ];
    }
}
