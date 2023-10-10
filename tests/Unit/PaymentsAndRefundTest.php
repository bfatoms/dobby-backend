<?php

namespace Tests\Unit;

use App\Models\Bill;
use App\Models\Order;
use Tests\AdminBaseTest;
use App\Models\Payment;
use App\Models\ChartOfAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentsAndRefundTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function test_bill_payment_index_is_happy()
    {
        $response = $this->get('/api/payments');

        $response->assertStatus(200);
    }

    public function testPayMultipleBillsIsHappy()
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

        $response = $this->post('/api/payments', $payment);

        $response->assertStatus(200);

        $response->assertJsonFragment(['status' => 'APPROVED']);

        $response->assertJsonFragment(['status' => 'PAID']);

        $response->assertSee("BATCH_PAYMENT_SUCCESSFUL");
    }

    public function testBillPaymentCreateMustBeTheSameOrderTypes()
    {
        // create order
        $orders = Order::factory()->count(3)->orderLines()->bill()->make()->toArray();

        $orders[1]['order_type'] = 'INV';

        foreach ($orders as $order) {
            $response = $this->post('/api/orders', $order);

            $order_data[] = ['id' => $response->original['data']->id, 'amount' => 1000];
        }

        $payment = Payment::factory()->make()->toArray();

        $payment['orders'] = $order_data;

        $response = $this->post('/api/payments', $payment);

        $response->assertStatus(422);

        $response->assertSee("ALL_ORDERS_MUST_BE_THE_SAME_ORDER_TYPES");
    }

    // TESTS FOR REFUND

    public function testBillRefundChartOfAccountIsRequired()
    {
        // create order
        $order = Order::factory()->orderLines()->bill()->make()->toArray();

        $response = $this->post("/api/orders", $order);

        $data = $response->original['data']->toArray();

        $refund = [
            'amount' => (float) $data['total_amount'],
            'paid_at' => now()->toDateTimeString()
        ];

        $response = $this->post("/api/orders/{$data['id']}/refund", $refund);

        $response->assertStatus(422);

        $response->assertSee("CHART_OF_ACCOUNT_REQUIRED");
    }

    public function testBillCreditNoteOverPaymentsPrePaymentsOnlyCanBeRefunded()
    {
        // create order
        $order = Order::factory()->orderLines()->bill()->make()->toArray();

        $response = $this->post("/api/orders", $order);

        $data = $response->original['data']->toArray();

        $refund = [
            'amount' => (float) $data['total_amount'],
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->create(['type' => 'bank'])->id
        ];

        $response = $this->post("/api/orders/{$data['id']}/refund", $refund);

        $response->assertStatus(422);

        $response->assertSee("ONLY_CREDIT_NOTES_OVERPAYMENTS_PREPAYMENTS_CAN_BE_REFUNDED");
    }

    public function test_bill_credit_note_refund_is_happy()
    {
        $order = Order::factory()->billCn()->create([
            'total_amount' => 10000
        ]);

        $refund = [
            'amount' => (float) 5000,
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id
        ];

        $response = $this->post("/api/orders/{$order['id']}/refund", $refund);

        $response->assertStatus(200);

        $response->assertSee("REFUND_SUCCESSFUL");

        $response = $this->get("/api/orders/{$order['id']}?with=refunds");

        $response->assertJsonFragment([
            "amount" => "5000.0000",
            "type" => "single-payment",
            "payment_type" => "refund",
            "balance" => 5000,
        ]);
    }

    public function test_spend_money_overpayment_refund_is_happy()
    {
        $order = Order::factory()->spendMoneyOverpayment()->create([
            'total_amount' => 10000
        ]);

        $refund = [
            'amount' => (float) 5000,
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id
        ];

        $response = $this->post("/api/orders/{$order['id']}/refund", $refund);

        $response->assertStatus(200);

        $response->assertSee("REFUND_SUCCESSFUL");

        $response = $this->get("/api/orders/{$order['id']}?with=refunds");

        $response->assertJsonFragment([
            "amount" => "5000.0000",
            "type" => "single-payment",
            "payment_type" => "refund",
            "balance" => 5000,
        ]);
    }

    public function test_spend_money_prepayment_refund_is_happy()
    {
        $order = Order::factory()->spendMoneyPrepayment()->create([
            'total_amount' => 10000
        ]);

        $refund = [
            'amount' => (float) 5000,
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id
        ];

        $response = $this->post("/api/orders/{$order['id']}/refund", $refund);

        $response->assertStatus(200);

        $response->assertSee("REFUND_SUCCESSFUL");

        $response = $this->get("/api/orders/{$order['id']}?with=refunds");

        $response->assertJsonFragment([
            "amount" => "5000.0000",
            "type" => "single-payment",
            "payment_type" => "refund",
            "balance" => 5000,
        ]);
    }

    public function test_receive_money_overpayment_refund_is_happy()
    {
        $order = Order::factory()->receiveMoneyOverpayment()->create([
            'total_amount' => 10000
        ]);

        $refund = [
            'amount' => (float) 5000,
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id
        ];

        $response = $this->post("/api/orders/{$order['id']}/refund", $refund);

        $response->assertStatus(200);

        $response->assertSee("REFUND_SUCCESSFUL");

        $response = $this->get("/api/orders/{$order['id']}?with=refunds");

        $response->assertJsonFragment([
            "amount" => "5000.0000",
            "type" => "single-payment",
            "payment_type" => "refund",
            "balance" => 5000,
        ]);
    }

    public function test_receive_money_prepayment_refund_is_happy()
    {
        $order = Order::factory()->receiveMoneyPrepayment()->create([
            'total_amount' => 10000
        ]);

        $refund = [
            'amount' => (float) 5000,
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id
        ];

        $response = $this->post("/api/orders/{$order['id']}/refund", $refund);

        $response->assertStatus(200);

        $response->assertSee("REFUND_SUCCESSFUL");

        $response = $this->get("/api/orders/{$order['id']}?with=refunds");

        $response->assertJsonFragment([
            "amount" => "5000.0000",
            "type" => "single-payment",
            "payment_type" => "refund",
            "balance" => 5000,
        ]);
    }

    public function test_bill_credit_note_refund_credit_note_payment_is_happy()
    {
        $order = Order::factory()->billCn()->create([
            'total_amount' => 10000,
            'currency_id' => $this->setting['currency_id']
        ]);

        $bill = Order::factory()->bill()->create([
            'total_amount' => 10000,
            'currency_id' => $this->setting['currency_id']
        ]);

        $refund = [
            'amount' => (float) 5000,
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id
        ];

        $response = $this->post("/api/orders/{$order['id']}/refund", $refund);

        $response->assertStatus(200);

        $response->assertSee("REFUND_SUCCESSFUL");

        // create 
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $order['id'],
            'orders' => [
                [
                    'id' => $bill['id'],
                    'amount' => 3000
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("BATCH_PAYMENT_SUCCESSFUL");

        $response = $this->get("/api/orders/{$order['id']}?with=refunds&creditNotePayments");

        $response->assertJsonFragment([
            "amount" => "5000.0000",
            "type" => "single-payment",
            "payment_type" => "refund",
            "balance" => 2000,
        ]);
    }

    public function test_spend_money_overpayment_refund_and_as_payment_is_happy()
    {
        $order = Order::factory()->spendMoneyOverpayment()->create([
            'total_amount' => 10000,
            'currency_id' => $this->setting['currency_id']
        ]);

        $bill = Order::factory()->bill()->create([
            'total_amount' => 10000,
            'currency_id' => $this->setting['currency_id']
        ]);

        $refund = [
            'amount' => (float) 5000,
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id
        ];

        $response = $this->post("/api/orders/{$order['id']}/refund", $refund);

        $response->assertStatus(200);

        $response->assertSee("REFUND_SUCCESSFUL");

        // create 
        $payment = [
            'paid_at' => now()->format("Y-m-d 00:00:00"),
            'credit_note_id' => $order['id'],
            'orders' => [
                [
                    'id' => $bill['id'],
                    'amount' => 1000
                ]
            ]
        ];

        $response = $this->post("/api/payments/credit-notes", $payment);

        $response->assertSee("BATCH_PAYMENT_SUCCESSFUL");

        $response = $this->get("/api/orders/{$order['id']}?with=refunds&creditNotePayments");

        $response->assertJsonFragment([
            "amount" => "5000.0000",
            "type" => "single-payment",
            "payment_type" => "refund",
            "balance" => 4000,
        ]);
    }
}
