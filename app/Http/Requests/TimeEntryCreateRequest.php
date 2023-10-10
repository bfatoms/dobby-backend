<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TimeEntryCreateRequest extends FormRequest
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
            'user_id' => auth()->user()->isAllowedTo('projects', 'create') ? 'required' : '',
            'task_id' => 'required',
            'type' => [
                'required',
                Rule::in(config('company.time_entry_types'))
            ],
            'start_at' => 'required_if:type,==,START_END|date|nullable',
            'end_at' => 'required_if:type,==,START_END|date|nullable',
            'duration' => 'required_if:type,==,DURATION|numeric',
            'time_entry_date' => 'required',
            'is_invoiced' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'USER_ID_REQUIRED',
            'task_id.required' => 'TASK_ID_REQUIRED',
            'type.required' => 'TYPE_REQUIRED',
            'type.in' => 'TYPE_IS_INVALID',
            'start_at.required_if' => 'START_AT_REQUIRED',
            'end_at.required_if' => 'END_AT_REQUIRED',
            'start_at.date' => 'START_AT_INVALID_DATE',
            'end_at.date' => 'END_AT_INVALID_DATE',
            'duration.required_if' => 'DURATION_REQUIRED',
            'time_entry_date.required' => 'TIME_ENTRY_DATE_REQUIRED'
        ];
    }
}
