<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ChartOfAccount;

class BillCreateRequest extends FormRequest
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
        $statuses = implode(',', config('company.ORDER_TYPES')[request('order_type')]['ALLOWED_STATUS_CREATE']);

        $m = [
            'order_type' => ['required'],
            'order_date' => ['required'],
            'status' => ["required", "in:{$statuses}"],
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
            'order_lines.*.chart_of_account_id' => [
                'required_if:status,APPROVED,FOR_APPROVAL',
                function ($attribute, $value, $fail) {
                    // Do not allow the inventory account on invoice
                    $account = ChartOfAccount::find($value);
                    if ($account['type'] != 'inventory' && request('is_tracked') == true) {
                        $fail('ACCOUNT_MUST_BE_INVENTORY_ACCOUNT_TYPE');
                    }
                },
            ],
            'contact_id' => 'required_without:contact_name',
            'contact_name' => 'required_without:contact_id',
            'order_lines' => 'required_unless:status,DRAFT',
            'total_amount' => 'required',
            'total_tax' => 'required',
        ];

        return $m;
    }

    public function messages()
    {
        $status = strtoupper(request('status'));

        return [
            'status.in' => "NOT_ALLOWED_TO_CREATE_STATUS_{$status}",
            'order_date.required' => 'ORDER_DATE_REQUIRED',
            'order_lines.*.quantity.required_if' => 'QUANTITY_REQUIRED_WHEN_STATUS_IS_' . $status,
            'order_lines.*.tax_rate.required_if' => 'TAX_RATE_REQUIRED_WHEN_STATUS_IS_' . $status,
            'order_lines.*.discount.required_if' => 'DISCOUNT_REQUIRED_WHEN_STATUS_IS_' . $status,
            'order_lines.*.tax_rate_id.required_if' => 'TAX_RATE_ID_REQUIRED_WHEN_STATUS_IS_' . $status,
            'order_lines.*.chart_of_account.required_if' => 'CHART_OF_ACCOUNT_ID_REQUIRED_WHEN_STATUS_IS_' . $status,
            'contact_id.required_without' => 'CONTACT_ID_REQUIRED',
            'contact_name.required_without' => 'CONTACT_NAME_REQUIRED',
            'order_type.required' => 'ORDER_TYPE_REQUIRED',
            'order_lines.required_unless' => 'ORDER_LINE_REQUIRED_WHEN_STATUS_IS_NOT_DRAFT',
            'total_amount.required' => 'TOTAL_AMOUNT_REQUIRED',
            'total_tax.required' => 'TOTAL_TAX_REQUIRED'
        ];
    }
}
