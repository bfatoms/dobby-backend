<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundCreateRequest extends FormRequest
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
            'chart_of_account_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'AMOUNT_REQUIRED',
            'amount.numeric' => 'AMOUNT_MUST_BE_NUMERIC',
            'paid_at.required' => 'PAID_AT_REQUIRED',
            'chart_of_account_id.required' => 'CHART_OF_ACCOUNT_REQUIRED'
        ];
    }
}
