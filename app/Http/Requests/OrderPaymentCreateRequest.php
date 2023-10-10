<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class OrderPaymentCreateRequest extends FormRequest
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
            'paid_at' => 'required',
            'credit_note_id' => 'required',
            'orders.*.id' => 'required',
            'orders.*.amount' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'orders.*.amount.required' => 'AMOUNT_REQUIRED',
            'orders.*.amount.numeric' => 'AMOUNT_MUST_BE_NUMERIC',
            'paid_at.required' => 'PAID_AT_REQUIRED',
            'chart_of_account_id.required' => 'CHART_OF_ACCOUNT_REQUIRED'
        ];
    }
}
