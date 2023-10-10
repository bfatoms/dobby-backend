<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentCreateRequest;
use App\Http\Requests\PaymentUpdateRequest;
use App\Http\Requests\RefundCreateRequest;
use App\Http\Requests\SinglePaymentCreateRequest;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentController extends BaseController
{
    protected $model = Payment::class;

    protected $create_request = PaymentCreateRequest::class;

    protected $update_request = PaymentUpdateRequest::class;

    public function pay(SinglePaymentCreateRequest $request, $order)
    {
        $order = Order::find($order);

        // only bills, inv, overpayment and prepayment can have payments
        if (in_array($order['order_type'], ['INV', 'BILL', 'SMO', 'SMP', 'RMP', 'RMO'])) {
            // account - nullable, but required when its not a CN/OP/PP payment (does not need to be a bank account)
            if (in_array($order['order_type'], ['INV', 'BILL']) && empty(request('chart_of_account_id'))) {
                return $this->reject('CHART_OF_ACCOUNT_REQUIRED');
            }

            $order = $this->addPayments($request, $order);

            return $this->resolve($order, 'PAYMENT_ADDED');
        }

        return $this->reject('ONLY_BILLS_INVOICE_OVERPAYMENT_PREPAYMENTS_CAN_BE_PAID');
    }

    public function store()
    {
        $id = request('orders.0.id');

        $first_order = Order::find($id);

        $this->authorize('create', [$this->model, $first_order['order_type']]);

        if (isset($this->create_request)) {
            $request = (new $this->create_request);

            request()->validate($request->rules(), $request->messages());
        }

        try {
            DB::beginTransaction();

            $data = request()->all();
            // create payment
            if (request('credit_note_id')) {
                $data =  $data + ['order_id' => request('credit_note_id')];
                $credit_note = Order::find(request('credit_note_id'));
            }

            $payment = Payment::create($data);

            $type = (count(request('orders')) > 1) ? 'batch-payment' : 'single-payment';

            foreach (request('orders') as $request_order) {
                $order = Order::with('payments', 'orderLines')->find($request_order['id']);

                $data['amount'] = $request_order['amount'];

                if (request('credit_note_id')) {
                    $credit_note_balance = $credit_note->getCreditNoteBalance();

                    if (round($credit_note_balance, 2) < round($data['amount'], 2)) {
                        abort(422, 'CREDIT_NOTE_BALANCE_INSUFFICIENT');
                    }
                }

                $payments[] = $this->addPayments($data, $order, $payment, $type);

                if (request('credit_note_id')) {
                    $credit_note_balance = $credit_note->getCreditNoteBalance();

                    if (round($credit_note_balance, 2) <= 0) {
                        $credit_note->status = "PAID";
                        $credit_note->save();
                    }
                }
            }

            DB::commit();

            return $this->resolve($payments, "BATCH_PAYMENT_SUCCESSFUL");
        } catch (Exception $ex) {
            DB::rollback();

            return $this->reject($ex->getMessage());
        }
    }

    public function addPayments($request, Order $order, Payment $payment = null, $type = 'single-payment')
    {
        if ($request['amount'] > round($order->getAmountDue(), 2)) {
            abort(422, 'PAYMENT_AMOUNT_MUST_BE_LOWER_OR_EQUAL_TO_BALANCE');
        }

        if (empty($payment)) {
            $payment = Payment::create($request->all());
        }

        $order->payments()->attach(
            $payment['id'],
            [
                'amount' => $request['amount'],
                'type' => $type
            ]
        );

        if ($order->getAmountDue() <= 0) {
            $order->status = "PAID";
            // we need to sleep 1 second because we don't store the micro seconds, see order_activities on the database for more info
            sleep(1);
            $order->save();
        }

        return $order;
    }

    public function refund(RefundCreateRequest $request, $order)
    {
        // only credit note, smo, smp, rmo, rmp can be refunded
        $order = Order::with('payments')->find($order);

        if (in_array($order['order_type'], ['SMO', 'RMO', 'SMP', 'RMP', 'BILL-CN', 'INV-CN'])) {
            // account - nullable, but required when its not a CN/OP/PP payment (does not need to be a bank account)
            if (in_array($order['order_type'], ['INV-CN', 'BILL-CN']) && empty(request('chart_of_account_id'))) {
                return $this->reject('CHART_OF_ACCOUNT_REQUIRED');
            }

            $order = $this->addRefunds($request, $order);

            return $this->resolve($order, 'REFUND_SUCCESSFUL');
        }

        return $this->reject('ONLY_CREDIT_NOTES_OVERPAYMENTS_PREPAYMENTS_CAN_BE_REFUNDED');
    }

    public function addRefunds($request, Order $order)
    {
        if ($request['amount'] > round($order->getCreditNoteBalance(), 2)) {
            abort(422, 'REFUND_AMOUNT_MUST_BE_LOWER_OR_EQUAL_TO_BALANCE');
        }

        $payment = Payment::create($request->all());

        $order->payments()->attach(
            $payment['id'],
            [
                'amount' => $request['amount'],
                'type' => 'single-payment',
                'payment_type' => 'refund'
            ]
        );

        if ($order->getCreditNoteBalance() <= 0) {
            $order->status = "PAID";
            sleep(1);
            $order->save();
        }

        return $order;
    }


}
