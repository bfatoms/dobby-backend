<?php

namespace Tests\Unit;

use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ChartOfAccount;
use App\Models\OrderLine;

class CreditNoteAsPaymentTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testBillPaymentCreateMustBeTheSameOrderTypes()
    {
        // create order
        $orders = Order::factory()->count(3)->orderLines()->bill()->make()->toArray();

        $orders[1]['order_type'] = 'INV';

        foreach ($orders as $order) {
            $response = $this->post('/api/orders', $order);

            $order_data[] = [
                'id' => $response->original['data']->id,
                'amount' => 1000
            ];
        }

        $payment = Payment::factory()->make()->toArray();

        $payment['orders'] = $order_data;

        $response = $this->post('/api/payments/credit-notes', $payment);

        $response->assertStatus(422);

        $response->assertSee("ALL_ORDERS_MUST_BE_THE_SAME_ORDER_TYPES");
    }

    public function testCreditNoteAsPaymentMustHaveTheSameCurrency()
    {
        $order_data = Order::factory()->orderLines()->billCn()->make()->toArray();

        $order = $this->post("/api/orders", $order_data);

        $bill = $order->original['data']->toArray();

        $order_data['currency_id'] = Currency::factory()->create()->id;

        $order = $this->post("/api/orders", $order_data);

        $credit = $order->original['data']->toArray();
        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $credit['id'],
            'orders' => [
                [
                    'id' => $bill['id'],
                    'amount' => $bill['total_amount']
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("CURRENCY_MUST_BE_THE_SAME_WITH_CREDIT_NOTE");
    }

    public function testBillCanBePaidWithBillCreditNote()
    {
        // create order
        $order_data_cn = $order_data = Order::factory()->orderLines()->bill()->make()->toArray();

        $order_data_cn['order_type'] = 'BILL-CN';

        $order = $this->post("/api/orders", $order_data);
        
        $bill = $order->original['data']->toArray();

        $order = $this->post("/api/orders", $order_data_cn);

        $credit = $order->original['data']->toArray();
        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $credit['id'],
            'orders' => [
                [
                    'id' => $bill['id'],
                    'amount' => $bill['total_amount']
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("BATCH_PAYMENT_SUCCESSFUL");
    }

    public function testBillCannotBePaidWithBill()
    {        // create order
        $order_data = Order::factory()->orderLines()->bill()->make()->toArray();

        $order = $this->post("/api/orders", $order_data);

        $bill = $order->original['data']->toArray();

        $order = $this->post("/api/orders", $order_data);

        $credit = $order->original['data']->toArray();
        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $credit['id'],
            'orders' => [
                [
                    'id' => $bill['id'],
                    'amount' => $bill['total_amount']
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("BILLS_CAN_ONLY_BE_PAID_WITH_BILL_CREDIT_NOTE_SPEND_MONEY_OVERPAYMENT_SPEND_MONEY_PREPAYMENT");
    }

    public function testBillCanBePaidWithSpendMoneyOverPayment()
    {
        $bill = Order::factory()->bill()->create([
            'total_amount' => 15000,
        ]);

        OrderLine::factory()->create([
            'order_id' => $bill['id']
        ]);

        $smo = Order::factory()->spendMoneyOverpayment()->create([
            'currency_id' => $bill['currency_id'],
            'total_amount' => $bill['total_amount'],
            'bank_id' => ChartOfAccount::factory()->spendMoney()->create()->id
        ]);

        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $smo['id'],
            'orders' => [
                [
                    'id' => $bill['id'],
                    'amount' => $bill['total_amount']
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("BATCH_PAYMENT_SUCCESSFUL");
    }

    public function testBillCannotBePaidWithSpendMoneyDirectPayment()
    {
        $bill = Order::factory()->bill()->create([
            'total_amount' => 15000,
        ]);

        OrderLine::factory()->create([
            'order_id' => $bill['id']
        ]);

        $smo = Order::factory()->spendMoneyDirect()->create([
            'currency_id' => $bill['currency_id'],
            'total_amount' => $bill['total_amount'],
            'bank_id' => ChartOfAccount::factory()->spendMoney()->create()->id
        ]);
        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $smo['id'],
            'orders' => [
                [
                    'id' => $bill['id'],
                    'amount' => $bill['total_amount']
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("BILLS_CAN_ONLY_BE_PAID_WITH_BILL_CREDIT_NOTE_SPEND_MONEY_OVERPAYMENT_SPEND_MONEY_PREPAYMENT");
    }

    public function testBillCanBePaidWithSpendMoneyPrePayment()
    {        // create order
        $bill = Order::factory()->bill()->create([
            'total_amount' => 15000,
        ]);

        OrderLine::factory()->create([
            'order_id' => $bill['id']
        ]);

        $smo = Order::factory()->spendMoneyPrepayment()->create([
            'currency_id' => $bill['currency_id'],
            'total_amount' => $bill['total_amount'],
            'bank_id' => ChartOfAccount::factory()->spendMoney()->create()->id
        ]);
        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $smo['id'],
            'orders' => [
                [
                    'id' => $bill['id'],
                    'amount' => $bill['total_amount']
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("BATCH_PAYMENT_SUCCESSFUL");
    }

    public function testInvoiceCannotBePaidWithInvoice()
    {        // create order
        $order_data = Order::factory()->orderLines()->invoice()->make()->toArray();

        $order = $this->post("/api/orders", $order_data);

        $invoice = $order->original['data']->toArray();

        $order = $this->post("/api/orders", $order_data);

        $credit = $order->original['data']->toArray();
        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $credit['id'],
            'orders' => [
                [
                    'id' => $invoice['id'],
                    'amount' => $invoice['total_amount']
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("INVOICES_CAN_ONLY_BE_PAID_WITH_INVOICE_CREDIT_NOTE_RECEIVE_MONEY_OVERPAYMENT_RECEIVE_MONEY_PREPAYMENT");
    }

    public function testInvoiceCanBePaidWithReceiveMoneyOverPayment()
    {        // create order
        $invoice = Order::factory()->invoice()->create([
            'total_amount' => 15000,
        ]);

        OrderLine::factory()->create([
            'order_id' => $invoice['id']
        ]);

        $rmo = Order::factory()->receiveMoneyOverpayment()->create([
            'currency_id' => $invoice['currency_id'],
            'total_amount' => $invoice['total_amount'],
            'bank_id' => ChartOfAccount::factory()->receiveMoney()->create()->id
        ]);
        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $rmo['id'],
            'orders' => [
                [
                    'id' => $invoice['id'],
                    'amount' => $invoice['total_amount']
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("BATCH_PAYMENT_SUCCESSFUL");
    }

    public function testInvoiceCannotBePaidWithReceiveMoneyDirectPayment()
    {
        $invoice = Order::factory()->invoice()->create([
            'total_amount' => 15000,
        ]);

        OrderLine::factory()->create([
            'order_id' => $invoice['id']
        ]);

        $rmo = Order::factory()->receiveMoneyDirect()->create([
            'currency_id' => $invoice['currency_id'],
            'total_amount' => $invoice['total_amount'],
            'bank_id' => ChartOfAccount::factory()->receiveMoney()->create()->id
        ]);
        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $rmo['id'],
            'orders' => [
                [
                    'id' => $invoice['id'],
                    'amount' => $invoice['total_amount']
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("INVOICES_CAN_ONLY_BE_PAID_WITH_INVOICE_CREDIT_NOTE_RECEIVE_MONEY_OVERPAYMENT_RECEIVE_MONEY_PREPAYMENT");
    }

    public function testInvoiceCanBePaidWithReceiveMoneyPrePayment()
    {
        // create order
        $invoice = Order::factory()->invoice()->create([
            'total_amount' => 15000,
        ]);

        OrderLine::factory()->create([
            'order_id' => $invoice['id']
        ]);

        $rmo = Order::factory()->receiveMoneyPrepayment()->create([
            'currency_id' => $invoice['currency_id'],
            'total_amount' => $invoice['total_amount'],
            'bank_id' => ChartOfAccount::factory()->receiveMoney()->create()->id
        ]);
        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $rmo['id'],
            'orders' => [
                [
                    'id' => $invoice['id'],
                    'amount' => $invoice['total_amount']
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("BATCH_PAYMENT_SUCCESSFUL");
    }

    public function testBillCreditNoteFullRefundIsHappy()
    {
        // create order
        $order = Order::factory()->orderLines()->billCn()->make()->toArray();

        $response = $this->post("/api/orders", $order);

        $data = $response->original['data']->toArray();

        $refund = [
            'amount' => (float) $data['total_amount'],
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->create(['type' => 'bank'])->id
        ];

        $response = $this->post("/api/orders/{$data['id']}/refund", $refund);

        $response->assertStatus(200);
    }

    public function testBillCreditNoteCannotBeVoidedOrDeleted()
    {
        // create order
        $order = Order::factory()->orderLines()->billCn()->make()->toArray();

        $response = $this->post("/api/orders", $order);

        $order = $response->original['data']->toArray();

        $refund = [
            'amount' => (float) $order['total_amount'] - 1,
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->create(['type' => 'bank'])->id
        ];

        $this->post("/api/orders/{$order['id']}/refund", $refund);

        $order['status'] = 'VOID';

        $response = $this->put("/api/orders/{$order['id']}", $order);

        $response->assertStatus(422);

        $response->assertSee('ORDER_WITH_PAYMENTS_CANT_BE_VOIDED');
    }

    public function testBillMultipleCreditNotePaymentCreateIsHappy()
    {
        // create order
        $orders = Order::factory()->count(3)->orderLines()->bill()->make()->toArray();

        foreach ($orders as $order) {
            $response = $this->post('/api/orders', $order);

            $order_data[] = ['id' => $response->original['data']->id, 'amount' => 1000];
        }

        $order_data[2]['amount'] = 1001;

        $order_data[1]['amount'] = round($orders[1]['total_amount'], 2);

        $payment = Payment::factory()->make()->toArray();

        $payment['orders'] = $order_data;

        $response = $this->post('/api/payments/credit-notes', $payment);

        $response->assertStatus(200);

        $response->assertJsonFragment(['status' => 'APPROVED']);

        $response->assertJsonFragment(['status' => 'PAID']);

        $response->assertSee("BATCH_PAYMENT_SUCCESSFUL");
    }

    public function testBillPaidWithBillCreditNotePaymentBalanceMustBeCorrect()
    {
        $bill = Order::factory()->bill()->create([
            'total_amount' => 10000,
            'currency_id' => $this->setting['currency_id']
        ]);

        // create another bill
        $bill2 = Order::factory()->bill()->create([
            'total_amount' => 20000,
            'currency_id' => $this->setting['currency_id']
        ]);

        $credit =Order::factory()->billCn()->create([
            'total_amount' => 5000,
            'currency_id' => $this->setting['currency_id']
        ]);

        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $credit['id'],
            'orders' => [
                [
                    'id' => $bill['id'],
                    'amount' => 3000
                ],
                [
                    'id' => $bill2['id'],
                    'amount' => 1000
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("BATCH_PAYMENT_SUCCESSFUL");

        $bill_balance = Order::find($bill['id'])->getAmountDue();

        $bill_balance2 = Order::find($bill2['id'])->getAmountDue();

        $credit_balance = Order::find($credit['id'])->getCreditNoteBalance();

        $this->assertSame(floatval(7000), $bill_balance);

        $this->assertSame(floatval(19000), $bill_balance2);

        $this->assertSame(floatval(1000), $credit_balance);
    }

    public function testBillPaidWithInsufficientBillCreditNoteMustFail()
    {        // create bill that is worth 10000
        $bill = Order::factory()->bill()->create([
            'total_amount' => 10000,
            'currency_id' => $this->setting['currency_id']
        ]);

        // create another bill
        $bill2 = Order::factory()->bill()->create([
            'total_amount' => 20000,
            'currency_id' => $this->setting['currency_id']
        ]);

        $credit = Order::factory()->billCn()->create([
            'total_amount' => 5000,
            'currency_id' => $this->setting['currency_id']
        ]);
        
        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $credit['id'],
            'orders' => [
                [
                    'id' => $bill['id'],
                    'amount' => 3000
                ],
                [
                    'id' => $bill2['id'],
                    'amount' => 8000
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee('CREDIT_NOTE_BALANCE_INSUFFICIENT');
    }

    public function testBillCreditNoteAsPaymentStatusMustBePaidAfterFullyConsumedAndBillMustAlsoBeSetToPaid()
    {
        $bill = Order::factory()->bill()->create([
            'total_amount' => 3000,
            'currency_id' => $this->setting['currency_id']
        ]);
        
        // create another bill
        $bill2 = Order::factory()->bill()->create([
            'total_amount' => 20000,
            'currency_id' => $this->setting['currency_id']
        ]);
        
        $credit = Order::factory()->billCn()->create([
            'total_amount' => 5000,
            'currency_id' => $this->setting['currency_id']
        ]);
        
        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $credit['id'],
            'orders' => [
                [
                    'id' => $bill['id'],
                    'amount' => 3000
                ],
                [
                    'id' => $bill2['id'],
                    'amount' => 2000
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("BATCH_PAYMENT_SUCCESSFUL");

        $credit = Order::find($credit['id']);

        $bill = Order::find($bill['id']);

        $this->assertSame('PAID', $credit['status']);

        $this->assertSame('PAID', $bill['status']);
    }

    public function testBillCreditNoteUsedAsPaymentCannotBeVoidedOrDeleted()
    {
        $bill = Order::factory()->bill()->create([
            'total_amount' => 3000,
            'currency_id' => $this->setting['currency_id']
        ]);
        
        // create another bill
        $bill2 = Order::factory()->bill()->create([
            'total_amount' => 20000,
            'currency_id' => $this->setting['currency_id']
        ]);
        
        $credit = Order::factory()->billCn()->create([
            'total_amount' => 5000,
            'currency_id' => $this->setting['currency_id']
        ]);

        // create payment
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $credit['id'],
            'orders' => [
                [
                    'id' => $bill['id'],
                    'amount' => 3000
                ],
                [
                    'id' => $bill2['id'],
                    'amount' => 1000
                ]
            ]
        ];

        $this->post("/api/payments/credit-notes", $payment);

        $response = $this->delete("/api/orders/" . $credit['id']);

        $response->assertSee("CREDIT_NOTES_USED_AS_PAYMENTS_CANT_BE_VOIDED_OR_DELETED");
    }
}
