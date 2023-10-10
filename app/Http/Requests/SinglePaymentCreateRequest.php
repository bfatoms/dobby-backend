<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SinglePaymentCreateRequest extends FormRequest
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
            'amount' => 'required|numeric',
            'paid_at' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'AMOUNT_REQUIRED',
            'amount.numeric' => 'AMOUNT_MUST_BE_NUMERIC',
            'paid_at.required' => 'PAID_AT_REQUIRED',
        ];
    }
}
