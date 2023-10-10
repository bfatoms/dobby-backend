<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectSettingUpdateRequest extends FormRequest
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
            'sales_price' => 'numeric|nullable',
            'purchase_price' => 'numeric|nullable'
        ];
    }

    public function messages()
    {
        return [
            'sales_price.numeric' => 'POSITIVE_AMOUNT_ONLY',
            'purchase_price.numeric' => 'POSITIVE_AMOUNT_ONLY',
        ];
    }
}
