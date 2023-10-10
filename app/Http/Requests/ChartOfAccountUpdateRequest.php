<?php

namespace App\Http\Requests;

use App\Models\ChartOfAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChartOfAccountUpdateRequest extends FormRequest
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
        if (request('type') == "bank") {
            return [
                'bank_name' => 'required',
                'name' => 'required',
                'code' => [
                    'required', 'min:3', 'alpha_num',
                    Rule::unique(ChartOfAccount::class)->ignore(request('chart_of_account'))
                ],    
                'type' => 'required',
                'currency_id' => 'required'
            ];    
        }

        return [
            'name' => 'required',
            'code' => [
                'required', 'min:3', 'max:10', 'alpha_num',
                Rule::unique(ChartOfAccount::class)->ignore(request('chart_of_account'))
            ],
            'type' => 'required',
            'tax_rate_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'NAME_REQUIRED',
            'code.required' => 'CODE_REQUIRED',
            'code.max' => 'CODE_MAX_10_CHARACTERS',
            'code.alpha_num' => 'CODE_ONLY_ALPHA_NUMERIC_ARE_ALLOWED',
            'code.unique' => 'CODE_EXISTS',
            'code.min' => 'CODE_MIN_3_CHARACTERS',
            'type.required' => 'TYPE_REQUIRED',
            'tax_rate_id.required' => 'TAX_RATE_REQUIRED',
            'bank_name.required' => 'BANK_NAME_REQUIRED',
            'currency_id.required' => 'CURRENCY_REQUIRED'
        ];
    }
}
