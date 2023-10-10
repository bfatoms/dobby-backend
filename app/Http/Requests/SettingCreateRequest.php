<?php

namespace App\Http\Requests;

use App\Rules\DueRule;
use App\Rules\DueTypeRule;
use Illuminate\Foundation\Http\FormRequest;

class SettingCreateRequest extends FormRequest
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
            'currency_id' => 'required',
            'bill_due' => ['required', new DueRule],
            'bill_due_type' => [new DueTypeRule],
            'invoice_due' => ['required', new DueRule],
            'invoice_due_type' => [new DueTypeRule],
            'quote_due' => ['required', new DueRule],
            'quote_due_type' => [new DueTypeRule],
            'invoice_prefix' => 'required',
            'invoice_next_number' => 'required|integer|min:1',
            'sales_order_prefix' => 'required',
            'sales_order_next_number' => 'required|integer|min:1',
            'purchase_order_prefix' => 'required',
            'purchase_order_next_number' => 'required|integer|min:1',
            'quote_prefix' => 'required',
            'quote_next_number' => 'required|integer|min:1',
            'credit_note_prefix' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'currency_id.required' => 'CURRENCY_REQUIRED',
            'invoice_prefix.required' => 'PREFIX_REQUIRED',
            'invoice_next_number.required' => 'NEXT_NUMBER_REQUIRED',
            'invoice_next_number.integer' => 'NEXT_NUMBER_MUST_BE_INTEGER',
            'invoice_next_number.min' => 'NEXT_NUMBER_MINIMUM_1',
            'sales_order_prefix.required' => 'PREFIX_REQUIRED',
            'sales_order_next_number.required' => 'NEXT_NUMBER_REQUIRED',
            'sales_order_next_number.integer' => 'NEXT_NUMBER_MUST_BE_INTEGER',
            'sales_order_next_number.min' => 'NEXT_NUMBER_MINIMUM_1',
            'purchase_order_prefix.required' => 'PREFIX_REQUIRED',
            'purchase_order_next_number.required' => 'NEXT_NUMBER_REQUIRED',
            'purchase_order_next_number.integer' => 'NEXT_NUMBER_MUST_BE_INTEGER',
            'purchase_order_next_number.min' => 'NEXT_NUMBER_MINIMUM_1',
            'quote_prefix.required' => 'PREFIX_REQUIRED',
            'quote_next_number.required' => 'NEXT_NUMBER_REQUIRED',
            'quote_next_number.integer' => 'NEXT_NUMBER_MUST_BE_INTEGER',
            'quote_next_number.min' => 'NEXT_NUMBER_MINIMUM_1',
            'credit_note_prefix.required' => 'PREFIX_REQUIRED',
            'bill_due.required' => 'BILL_DUE_REQUIRED',
            'invoice_due.required' => 'INVOICE_DUE_REQUIRED',
            'quote_due.required' => 'QUOTE_DUE_REQUIRED',
        ];
    }
}
