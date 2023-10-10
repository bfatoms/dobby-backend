<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Order;

class PaymentCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        $first_order = Order::find(request('orders.0.id'));

        $credit_note = null;

        if (request('credit_note_id')) {
            $credit_note = Order::find(request('credit_note_id'));

            return [
                'paid_at' => 'required',
                'orders.*.id' => ['required', function ($attribute, $value, $fail) use ($first_order) {
                    $order = Order::find($value);
                    if ($order['order_type'] != $first_order['order_type']) {
                        return $fail('ALL_ORDERS_MUST_BE_THE_SAME_ORDER_TYPES');
                    }
                }, function ($attribute, $value, $fail) use ($credit_note) {
                    $order = Order::find($value);
                    if ($order['order_type'] == 'BILL') {
                        if (!in_array($credit_note['order_type'], ['SMO', 'SMP', 'BILL-CN'])) {
                            return $fail("BILLS_CAN_ONLY_BE_PAID_WITH_BILL_CREDIT_NOTE_SPEND_MONEY_OVERPAYMENT_SPEND_MONEY_PREPAYMENT");
                        }
                    } elseif ($order['order_type'] == 'INV') {
                        if (!in_array($credit_note['order_type'], ['RMO', 'RMP', 'INV-CN'])) {
                            return $fail("INVOICES_CAN_ONLY_BE_PAID_WITH_INVOICE_CREDIT_NOTE_RECEIVE_MONEY_OVERPAYMENT_RECEIVE_MONEY_PREPAYMENT");
                        }
                    }
                }, function ($attribute, $value, $fail) use ($credit_note) {
                    $order = Order::find($value);
                    if ($order['currency_id'] != $credit_note['currency_id']) {
                        return $fail('CURRENCY_MUST_BE_THE_SAME_WITH_CREDIT_NOTE');
                    }
                }],
                'orders.*.amount' => 'required|numeric',
            ];
        }

        return [
            'paid_at' => 'required',
            'orders.*.id' => ['required', function ($attribute, $value, $fail) use ($first_order) {
                $order = Order::find($value);
                if ($order['order_type'] != $first_order['order_type']) {
                    return $fail('ALL_ORDERS_MUST_BE_THE_SAME_ORDER_TYPES');
                }
            }],
            'orders.*.amount' => 'required|numeric',
            'chart_of_account_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'orders.*.amount.required' => 'AMOUNT_REQUIRED',
            'orders.*.amount.numeric' => 'AMOUNT_MUST_BE_NUMERIC',
            'paid_at.required' => 'PAID_AT_REQUIRED',
            'chart_of_account_id.required' => 'CHART_OF_ACCOUNT_REQUIRED'
        ];
    }
}
