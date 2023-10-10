<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DueRule;
use App\Rules\DueTypeRule;

class ContactCreateRequest extends FormRequest
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
            'mobile_number' => ['regex:/^[0-9]+$/', 'nullable'],
            'contact_persons.*.first_name' => 'required',
            'contact_persons.*.last_name' => 'required',
            'contact_persons.*.email' => ['required', 'email'],
            'credit_limit' => ['numeric', 'min:0'],
            'sale_discount' => ['numeric', 'min:0'],
            'bill_due' => [new DueRule()],
            'invoice_due' => [new DueRule()],
            'bill_due_type' => [new DueTypeRule()],
            'invoice_due_type' => [new DueTypeRule()],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'NAME_REQUIRED',
            'mobile_number.regex' => 'MOBILE_NUMBER_ONLY_ACCEPT_NUMBERS',
            'contact_persons.*.first_name.required' => 'FIRST_NAME_REQUIRED',
            'contact_persons.*.last_name.required' => 'LAST_NAME_REQUIRED',
            'contact_persons.*.email.required' => 'EMAIL_REQUIRED',
            'contact_persons.*.email.email' => 'EMAIL_MUST_BE_A_VALID_EMAIL',
            'credit_limit.min' => 'CREDIT_LIMIT_MIN_VALUE_IS_0',
            'sale_discount.min' => 'SALE_DISCOUNT_MIN_VALUE_IS_0',
            'sale_discount.numeric' => 'SALE_DISCOUNT_MUST_BE_NUMERIC'
        ];
    }
}
