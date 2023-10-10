<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'project_id' => 'required',
            'name' => 'required',
            'quantity' => 'required|numeric',
            'unit_price' => 'required|numeric',
            'charge_type' => [
                'required',
                Rule::in(config('company.expense_charge_types'))
            ],
            'mark_up' => 'required_if:charge_type,==,MARK_UP|numeric',
            'custom_price' => 'required_if:charge_type,==,CUSTOM_PRICE|numeric'
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => 'PROJECT_ID_REQUIRED',
            'name.required' => 'NAME_REQUIRED',
            'quantity.required' => 'QUANTITY_REQUIRED',
            'quantity.numeric' => 'QUANTITY_MUST_BE_A_NUMBER',
            'unit_price.required' => 'UNIT_PRICE_REQUIRED',
            'unit_price.numeric' => 'UNIT_PRICE_MUST_BE_A_NUMBER',
            'mark_up.numeric' => 'MARK_UP_MUST_BE_A_NUMBER',
            'charge_type.required' => 'CHARGE_TYPE_REQUIRED',
            'charge_type.in' => 'CHARGE_TYPE_IS_INVALID',
            'mark_up.required_if' => 'MARK_UP_REQUIRED',
            'custom_price.required_if' => 'CUSTOM_PRICE_REQUIRED',
        ];
    }
}
