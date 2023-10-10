<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;

class OrderActivityViewTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testSpendMoneyDirectOrderActivityWithoutPaymentMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->spendMoneyDirect()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);
    }

    public function testReceiveMoneyDirectrderActivityWithoutPaymentMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->receiveMoneyDirect()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);
    }

    public function testInvoiceOrderActivityWithoutPaymentMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->invoice()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 10000.0000
        ]);
    }

    public function testInvoiceOrderActivityWithPaymentMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->invoice()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $payment = Payment::factory()->create();

        $order->payments()->attach($payment['id'], [
            'amount' => $order['total_amount']
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'payment',
            'total_amount' => 10000.0000,
            'amount_due' => null
        ]);
    }

    public function testBillOrderActivityWithoutPaymentMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->bill()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 10000.0000
        ]);
    }

    public function testBillOrderActivityWithPaymentMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->bill()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $payment = Payment::factory()->create();

        $order->payments()->attach($payment['id'], [
            'amount' => $order['total_amount']
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'payment',
            'total_amount' => 10000.0000,
            'amount_due' => null
        ]);
    }

    public function testInvoiceCreditNoteOrderActivityWithoutRefundMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->invoiceCn()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 10000.0000
        ]);
        
    }

    public function testInvoiceCreditNoteOrderActivityWithRefundMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->invoiceCn()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $payment = Payment::factory()->create();

        $order->payments()->attach($payment['id'], [
            'amount' => $order['total_amount']
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'refund',
            'total_amount' => 10000.0000,
            'amount_due' => null
        ]);
    }

    public function testBillCreditNoteOrderActivityWithoutRefundMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->billCn()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 10000.0000
        ]);
    }

    public function testBillCreditNoteOrderActivityWithRefundMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->billCn()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $payment = Payment::factory()->create();

        $order->payments()->attach($payment['id'], [
            'amount' => $order['total_amount']
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'refund',
            'total_amount' => 10000.0000,
            'amount_due' => null
        ]);
    }

    public function testSpendMoneyOverpaymentOrderActivityWithoutRefundMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->spendMoneyOverpayment()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);
    }

    public function testSpendMoneyOverpaymentOrderActivityWithRefundMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->spendMoneyOverpayment()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $payment = Payment::factory()->create();

        $order->payments()->attach($payment['id'], [
            'amount' => $order['total_amount']
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'refund',
            'total_amount' => 10000.0000,
            'amount_due' => null
        ]);
    }

    public function testSpendMoneyPrepaymentOrderActivityWithoutRefundMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->spendMoneyPrepayment()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);
    }

    public function testSpendMoneyPrepaymentOrderActivityWithRefundMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->spendMoneyPrepayment()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $payment = Payment::factory()->create();

        $order->payments()->attach($payment['id'], [
            'amount' => $order['total_amount']
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'refund',
            'total_amount' => 10000.0000,
            'amount_due' => null
        ]);
    }

    public function testReceiveMoneyOverpaymentOrderActivityWithoutRefundMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->receiveMoneyOverpayment()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);
    }

    public function testReceiveMoneyOverpaymentOrderActivityWithRefundMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->receiveMoneyOverpayment()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $payment = Payment::factory()->create();

        $order->payments()->attach($payment['id'], [
            'amount' => $order['total_amount']
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'refund',
            'total_amount' => 10000.0000,
            'amount_due' => null
        ]);
    }

    public function testReceiveMoneyPrepaymentOrderActivityWithoutRefundMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->receiveMoneyPrepayment()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);
    }

    public function testReceiveMoneyPrepaymentOrderActivityWithRefundMustShow()
    {
        $this->withoutExceptionHandling();

        $order = Order::factory()->receiveMoneyPrepayment()->approved()->create([
            'total_amount' => floatval(10000)
        ]);

        $payment = Payment::factory()->create();

        $order->payments()->attach($payment['id'], [
            'amount' => $order['total_amount']
        ]);

        $response = $this->json('GET', '/api/order-activities');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'order',
            'total_amount' => 10000.0000,
            'amount_due' => 0.0000
        ]);

        $response->assertJsonFragment([
            'id' => $order['id'],
            'activity_type' => 'refund',
            'total_amount' => 10000.0000,
            'amount_due' => null
        ]);
    }
}
