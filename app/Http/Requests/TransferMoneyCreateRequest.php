<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferMoneyCreateRequest extends FormRequest
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
            'from_bank_account_id' => 'required',
            'to_bank_account_id' => 'required',
            'from_amount' => 'required|numeric|min:0',
            'to_amount' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'from_bank_account_id.required' => 'FROM_ACCOUNT_REQUIRED',
            'to_bank_account_id.required' => 'TO_ACCOUNT_REQUIRED',
            'from_amount.required' => 'FROM_AMOUNT_REQUIRED',
            'from_amount.numeric' => 'POSITIVE_AMOUNT_ONLY',
            'from_amount.min' => 'POSITIVE_AMOUNT_ONLY',
            'to_amount.required' => 'TO_AMOUNT_REQUIRED',
            'to_amount.numeric' => 'POSITIVE_AMOUNT_ONLY',
            'to_amount.min' => 'POSITIVE_AMOUNT_ONLY',
            
        ];
    }
}
