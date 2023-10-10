<?php

namespace App\Http\Requests;

use App\Models\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyUpdateRequest extends FormRequest
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
            'code' => [
                'required',
                'max:3',
                'min:3',
                Rule::unique(Currency::class)->ignore(request('currency'))
            ],
            'symbol' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'NAME_REQUIRED',
            'code.required' => 'CODE_REQUIRED',
            'code.unique' => 'CODE_EXISTS',
            'code.min' => 'CODE_MUST_BE_A_MIN_3',
            'code.max' => 'CODE_MUST_BE_A_MAX_3',
            'symbol.required' => 'SYMBOL_REQUIRED'
        ];
    }
}
