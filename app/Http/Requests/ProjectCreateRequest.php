<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectCreateRequest extends FormRequest
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
            'name' => 'required',
            'contact_name' => 'required_without:contact_id',
            'status' => [
                'required',
                Rule::in(config('company.project_statuses'))
            ],
            'deadline' => 'date|nullable',
            'estimate' => 'numeric|nullable'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'PROJECT_NAME_REQUIRED',
            'status.required' => 'STATUS_REQUIRED',
            'status.in' => 'STATUS_IS_INVALID',
            'contact_name.required_without' => 'CONTACT_NAME_REQUIRED',
            'deadline.date' => 'DEADLINE_INVALID_DATE',
            'estimate.numeric' => 'ESTIMATE_MUST_BE_A_NUMBER'
        ];
    }
}
