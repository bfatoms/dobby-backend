<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitialOrderDataRequest extends FormRequest
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
            'order_type' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'order_type.required' => 'ORDER_TYPE_REQUIRED'
        ];
    }
}
