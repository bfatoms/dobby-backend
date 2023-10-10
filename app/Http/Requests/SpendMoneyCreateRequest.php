<?php

namespace App\Http\Requests;

use App\Models\ChartOfAccount;
use Illuminate\Foundation\Http\FormRequest;

class SpendMoneyCreateRequest extends FormRequest
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
        $statuses = implode(',', config('company.ORDER_TYPES')[request('order_type')]['ALLOWED_STATUS_CREATE']);

        request()->merge(['currency_id' => ChartOfAccount::find(request('bank_id'))['currency_id']]);

        $validations = [
            'order_type' => 'required',
            'order_date' => 'required',
            'status' => ["required", "in:{$statuses}"],
            'bank_id' => 'required',
            'order_lines' => 'required',
            'order_lines.*.quantity' => 'required_if:status,APPROVED,FOR_APPROVAL|nullable|numeric',
            'contact_id' => 'required_without:contact_name',
            'contact_name' => 'required_without:contact_id',
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
                    if ($account['type'] === 'inventory' && request('is_tracked') == true) {
                        $fail('INVENTORY_TYPE_ACCOUNT_IS_NOT_ALLOWED_ON_INVOICE');
                    }
                },
            ],
            'order_lines.*.unit_price' => "numeric|nullable|required_if:status,APPROVED,FOR_APPROVAL",
            'total_amount' => 'required',
        ];

        if (in_array(request('order_type'), ['SMO', 'SMP'])) {
            $validations['order_lines.*.product_id'] = [function ($attribute, $value, $fail) {
                if (!empty($value)) {
                    $fail('PRODUCT_MUST_BE_NULL_OR_EMPTY');
                }
            }];

            $validations['order_lines.*.chart_of_account_id'] = [
                'required_if:status,APPROVED,FOR_APPROVAL',
                function ($attribute, $value, $fail) {
                    // Do not allow the inventory account on invoice
                    $account = ChartOfAccount::find($value);
                    if ($account['type'] === 'inventory' && request('is_tracked') == true) {
                        $fail('INVENTORY_TYPE_ACCOUNT_IS_NOT_ALLOWED_ON_INVOICE');
                    }
                },

                function ($attribute, $value, $fail) {
                    $receivable = ChartOfAccount::where('system_name', 'accounts-receivable')->first();
                    if ($value != $receivable['id']) {
                        $fail('CHART_OF_ACCOUNT_MUST_BE_ACCOUNTS_RECEIVABLE');
                    }
                }
            ];
        }

        return $validations;
    }

    public function messages()
    {
        $status = strtoupper(request('status'));

        return [
            'order_type.required' => 'ORDER_TYPE_REQUIRED',
            'status.in' => "NOT_ALLOWED_TO_CREATE_STATUS_{$status}",
            'bank_id.required' => 'BANK_ID_REQUIRED',
            'order_lines.required' => 'ORDER_LINES_REQUIRED',
            'order_lines.*.quantity.required_if' => 'QUANTITY_REQUIRED_WHEN_STATUS_IS_' . $status,
            'contact_id.required_without' => 'CONTACT_ID_REQUIRED',
            'contact_name.required_without' => 'CONTACT_NAME_REQUIRED',
            'order_lines.*.tax_rate_id.required_if' => 'TAX_RATE_ID_REQUIRED_WHEN_STATUS_IS_' . $status,
            'order_lines.*.chart_of_account_id.required_if' => 'CHART_OF_ACCOUNT_ID_REQUIRED_WHEN_STATUS_IS_' . $status,
            'total_amount.required' => 'TOTAL_AMOUNT_REQUIRED',
            'order_lines.*.unit_price' => 'UNIT_PRICE_REQUIRED',
        ];
    }
}
