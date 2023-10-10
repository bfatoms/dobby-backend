<?php

namespace App\Http\Requests;

use App\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseUpdateRequest extends FormRequest
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

    public function rules()
    {
        $this->data = Purchase::find(request('purchase', request('order')));

        $type = $this->data['order_type'];

        $status = $this->data['status'];

        $statuses = implode(',', config('company.ORDER_TYPES')[$type]['ALLOWED_STATUS_UPDATE'][$status]);

        return [
            'status' => ['required', "in:{$statuses}," . $this->data['status']],
            'order_date' => 'required',
            'end_date' => [
                function ($attribute, $value, $fail) {
                    if(!in_array(request('order_type'), ['BILL-CN', 'INV-CN']) && empty($value)){
                        $fail('END_DATE_REQUIRED');
                    }
                }
            ],
            'order_lines.*.quantity' => 'required_if:status,APPROVED,FOR_APPROVAL|nullable|numeric',
            'order_lines.*.tax_rate' => [
                function ($attribute, $value, $fail) {
                    if (request('tax_setting') === 'no tax') {
                        if ($value != 0) {
                            $fail('TAX_RATE_MUST_BE_0_IF_TAX_SETTING_IS_NO_TAX');
                        }
                    }
                },
                "required_if:status,APPROVED,FOR_APPROVAL"
            ],
            'order_lines.*.discount' => "numeric|nullable",
            'order_lines.*.unit_price' => "numeric|nullable|required_if:status,APPROVED,FOR_APPROVAL",
            'order_lines.*.tax_rate_id' => [
                "required_if:status,APPROVED,FOR_APPROVAL",
                function ($attribute, $value, $fail) {
                    if (request('tax_setting') === 'no tax') {
                        if ($value != 1) {
                            $fail('TAX_RATE_ID_MUST_BE_TAX_EXEMPT');
                        }
                    }
                },
            ],
            'order_lines.*.chart_of_account_id' => 'required_if:status,APPROVED,FOR_APPROVAL',
            'contact_id' => 'required_without:contact_name',
            'contact_name' => 'required_without:contact_id',
            'order_lines' => 'required_unless:status,DRAFT',
        ];
    }

    public function messages()
    {
        $new_status = strtoupper(request('status'));

        $status = strtoupper($this->data['status']);

        return [
            'status.in' => "UPDATE_STATUS_FROM_{$status}_TO_{$new_status}_NOT_ALLOWED",
            'order_lines.*.quantity.required_if' => 'QUANTITY_REQUIRED_WHEN_STATUS_IS_' . $new_status,
            'order_lines.*.tax_rate.required_if' => 'TAX_RATE_REQUIRED_WHEN_STATUS_IS_' . $new_status,
            'order_lines.*.discount.required_if' => 'DISCOUNT_REQUIRED_WHEN_STATUS_IS_' . $new_status,
            'order_lines.*.tax_rate_id.required_if' => 'TAX_RATE_ID_REQUIRED_WHEN_STATUS_IS_' . $new_status,
            'contact_id.required_without' => 'CONTACT_ID_REQUIRED',
            'contact_name.required_without' => 'CONTACT_NAME_REQUIRED',
            'order_type.required' => 'ORDER_TYPE_REQUIRED',
            'order_lines.required_unless' => 'ORDER_LINE_REQUIRED_WHEN_STATUS_IS_NOT_DRAFT',
            'order_date.required' => 'ORDER_DATE_REQUIRED'
        ];
    }
}
