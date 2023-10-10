<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectPriceUpdateRequest extends FormRequest
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
            'sales_price' => 'numeric',
            'purchase_price' => 'numeric',
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => 'PROJECT_ID_REQUIRED',
            'sales_price.numeric' => 'SALES_PRICE_MUST_BE_A_NUMBER',
            'purchase_price.numeric' => 'PURCHASE_PRICE_MUST_BE_A_NUMBER',
        ];
    }
}
