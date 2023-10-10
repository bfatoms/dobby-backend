<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentUpdateRequest extends FormRequest
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
            'account_id' => [
                function ($attribute, $value, $fail) {
                    if (!in_array(request('order_type'), ['RMD', 'SMD', 'INV', 'BILL', 'SO', 'PO', 'QU'])) {
                        $fail('CHART_OF_ACCOUNT_REQUIRED');
                    }
                }
            ],
            'amount' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'account_id.required_unless' => 'CHART_OF_ACCOUNT_REQUIRED',
            'amount.required' => 'AMOUNT_REQUIRED',
            'amount.numeric' => 'AMOUNT_MUST_BE_NUMERIC'
        ];
    }
}
