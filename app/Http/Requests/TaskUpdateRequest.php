<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskUpdateRequest extends FormRequest
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
            'charge_type' => [
                'required',
                Rule::in(config('company.task_charge_types'))
            ],
            'status' => [
                'required',
                Rule::in(config('company.task_statuses'))
            ],
            'rate' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => 'PROJECT_ID_REQUIRED',
            'name.required' => 'NAME_REQUIRED',
            'charge_type.required' => 'CHARGE_TYPE_REQUIRED',
            'charge_type.in' => 'CHARGE_TYPE_IS_INVALID',
            'status.required' => 'STATUS_REQUIRED',
            'status.in' => 'STATUS_IS_INVALID',
            'rate.required' => 'RATE_REQUIRED',
            'rate.numeric' => 'RATE_MUST_BE_A_NUMBER'
        ];
    }
}
