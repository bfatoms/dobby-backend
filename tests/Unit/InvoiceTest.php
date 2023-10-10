<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use Tests\AdminBaseTest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Contact;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\TaxRate;

class InvoiceTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testInvoiceIndex()
    {
        $response = $this->get('/api/orders?order_type=INV');

        $response->assertStatus(200);
    }

    public function testInvoiceCreate()
    {
        $data = [
            'order_type' => 'INV',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'DRAFT',
            'total_amount' => 0.0,
            'total_tax' => 0.0
        ];

        $response = $this->post('/api/orders', $data);

        $response->assertSee('RESOURCE_CREATED');
    }

    public function testInvoiceCreateWithContactName()
    {
        $data = Order::factory()->orderLines()->approved()->invoice()->make()->toArray();

        $data['contact_name'] = "Test contact name";

        $response = $this->post('/api/orders', $data);

        $response->assertSee('RESOURCE_CREATED');
    }

    public function testInvoiceCreateWithError()
    {
        $data = Order::factory()->orderLines()->approved()->invoice()->make()->toArray();

        $data['currency_id'] = "0";

        $response = $this->post('/api/orders', $data);

        $response->assertSee('Integrity constraint violation');
    }

    public function testInvoiceCreateWithEmptyOrderType()
    {
        $data = Order::factory()->orderLines()->approved()->invoice()->make()->toArray();

        $data['order_type'] = null;

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(422);
    }

    public function testInvoiceCreateOrderDateRequired()
    {
        $data = [
            'order_type' => 'INV',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'DRAFT',
            'total_amount' => 0.0,
            'total_tax' => 0.0
        ];

        $response = $this->post('/api/orders', $data);

        $response->assertSee('ORDER_DATE_REQUIRED');
    }

    public function testInvoiceCreateWithStatusApproved()
    {
        $data = [
            'order_type' => 'INV',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'APPROVED',
        ];

        $response = $this->post('/api/orders', $data);

        $response->assertSee('ORDER_LINE_REQUIRED_WHEN_STATUS_IS_NOT_DRAFT');
    }

    public function testInvoiceCreateWithVoidStatusIsNotAllowed()
    {
        $data = [
            'order_type' => 'INV',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'VOID',
        ];

        $response = $this->post('/api/orders', $data);

        $response->assertSee('NOT_ALLOWED_TO_CREATE_STATUS_VOID');
    }

    public function testInvoiceCreateTaxRateMustBeExemptIfTaxSettingIsNoTax()
    {
        $data = [
            'order_type' => 'INV',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'DRAFT',
            'order_lines' => [
                OrderLine::factory()->make()->toArray()
            ]
        ];

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(422);

        $response->assertSee('TAX_RATE_MUST_BE_0_IF_TAX_SETTING_IS_NO_TAX');

        $response->assertSee('TAX_RATE_ID_MUST_BE_TAX_EXEMPT');
    }

    public function testInvoiceUpdate()
    {
        $data = [
            'order_type' => 'INV',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'DRAFT',
            'order_lines' => [
                OrderLine::factory()->make(['tax_rate' => 0, 'tax_rate_id' => 1])->toArray()
            ],
            'total_amount' => 0.0,
            'total_tax' => 0.0
        ];

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['status'] = 'VOID';

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(422);

        $response->assertSee('UPDATE_STATUS_FROM_DRAFT_TO_VOID_NOT_ALLOWED');
    }

    public function testInvoiceUpdateWithError()
    {
        $data = [
            'order_type' => 'INV',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'DRAFT',
            'order_lines' => [
                OrderLine::factory()->make(['tax_rate' => 0, 'tax_rate_id' => 1])->toArray()
            ],
            'total_amount' => 0.0,
            'total_tax' => 0.0
        ];

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['currency_id'] = '0';

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertSee('Integrity constraint violation');
    }

    public function testInvoiceCreateWithOrderLine()
    {
        $data = Order::factory()->orderLines()->make(['order_type' => 'INV'])->toArray();

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(200);

        $response->assertSee('quantity');

        $response->assertSee('unit_price');

        $response->assertSee('tax_rate');
    }

    public function testInvoiceCreateWithOrderLineWithStatusDraft()
    {
        $data = Order::factory()->invoice()->make()->toArray();

        $data['tax_setting'] = 'no tax';

        $data['order_lines'] = [[
            "product_id" => Product::factory()->create()->id,
            "description" => "Quod reprehenderit eaque iure enim.",
            "quantity" => null,
            "unit_price" => 1964,
            "discount" => 0.15,
            "tax_rate" => null,
        ]];

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(422);

        $response->assertSee('TAX_RATE_REQUIRED_WHEN_STATUS_IS_APPROVED');

        $response->assertSee('QUANTITY_REQUIRED_WHEN_STATUS_IS_APPROVED');
    }

    public function testInvoiceForApprovalDeleteMustBeVoided()
    {
        $data = Order::factory()->orderLines()->make([
            'status' => 'FOR_APPROVAL',
            'tax_setting' => 'tax inclusive',
            'order_type' => 'INV',
            'reference' => 'TEST CREATE'
        ])->toArray();

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['status'] = "VOID";

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee('VOID');
    }

    public function testInvoiceApprovedDeleteMustBeVoided()
    {
        $data = Order::factory()->orderLines()->make([
            'status' => 'APPROVED',
            'tax_setting' => 'tax inclusive',
            'order_type' => 'INV',
            'reference' => 'TEST CREATE'
        ])->toArray();

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $response = $this->delete('/api/orders/' . $data['id']);

        $response->assertStatus(200);

        $response->assertSee('VOID');
    }

    public function testQuoteSentDeleteMustBeVoided()
    {
        $data = Order::factory()->orderLines()->make([
            'status' => 'SENT',
            'tax_setting' => 'tax inclusive',
            'order_type' => 'QU',
            'reference' => 'TEST CREATE'
        ])->toArray();

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $response = $this->delete('/api/orders/' . $data['id']);

        $response->assertStatus(200);

        $response->assertSee('DELETED');
    }

    public function test_invoice_pay()
    {
        $bank = ChartOfAccount::factory()->bankAccount()->create();

        $data = Order::factory()->orderLines()->invoice()->make()->toArray();

        $total = 0;

        foreach ($data['order_lines'] as $line) {
            $total += $line['amount'];
        }

        $data['total_amount'] = $total;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $payment = [
            'amount' => (float) $data['total_amount'],
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => $bank['id']
        ];
        
        $response = $this->post("/api/orders/{$data['id']}/pay", $payment);

        $response->assertSee('PAYMENT_ADDED');

        $response->assertSee('PAID');
    }

    public function test_invoice_paid_cannot_be_deleted()
    {
        $bank = ChartOfAccount::factory()->bankAccount()->create();

        $data = Order::factory()->orderLines()->invoice()->make()->toArray();

        $total = 0;

        foreach ($data['order_lines'] as $line) {
            $total += $line['amount'];
        }

        $data['total_amount'] = $total;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $payment = [
            'amount' => (float) $data['total_amount'],
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => $bank['id']
        ];
        
        $this->post("/api/orders/{$data['id']}/pay", $payment);

        $response = $this->delete("/api/orders/{$data['id']}");

        $response->assertSee('ORDER_WITH_PAYMENTS_CANT_BE_VOIDED');
    }

    public function test_invoice_credit_note_pay_is_not_allowed()
    {
        $bank = ChartOfAccount::factory()->bankAccount()->create();

        $data = Order::factory()->orderLines()->invoiceCn()->make()->toArray();

        $total = 0;

        foreach ($data['order_lines'] as $line) {
            $total += $line['amount'];
        }

        $data['total_amount'] = $total;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $payment = [
            'amount' => (float) $data['total_amount'],
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => $bank['id']
        ];
        
        $response = $this->post("/api/orders/{$data['id']}/pay", $payment);

        $response->assertSee('ONLY_BILLS_INVOICE_OVERPAYMENT_PREPAYMENTS_CAN_BE_PAID');
    }

    public function test_invoice_tracked_product_available_quantity_must_be_nineteen_after_creating_credit_note()
    {
        $tax_rate = TaxRate::factory()->create();

        $product = Product::factory()->tracked()->create();

        $bill = Order::factory()->bill()->approved()->create();

        OrderLine::factory()->create([
            'order_id' => $bill['id'],
            'tax_rate' => 0,
            'tax_rate_id' => $tax_rate['id'],
            'product_id' => $product['id'],
            'quantity' => 20
        ]);

        $invoice = Order::factory()->invoice()->approved()->create();

        OrderLine::factory()->create([
            'order_id' => $invoice['id'],
            'tax_rate' => 0,
            'tax_rate_id' => $tax_rate['id'],
            'product_id' => $product['id'],
            'quantity' => 10
        ]);

        $invoice = Order::factory()->invoiceCn()->approved()->create();

        OrderLine::factory()->create([
            'order_id' => $invoice['id'],
            'tax_rate' => 0,
            'tax_rate_id' => $tax_rate['id'],
            'product_id' => $product['id'],
            'quantity' => -9
        ]);
        
        $response = $this->get('/api/products/'.$product['id']);

        $response->assertStatus(200);

        $response->assertJsonFragment(['available_quantity' => 19]);
    }

    public function test_invoice_must_have_amount_due_appended_on_get()
    {
        $invoice = Order::factory()->invoice()->create([
            'total_amount' => 12334
        ]);

        $response = $this->json('GET', '/api/orders/'.$invoice['id']);

        $response->assertJsonFragment([
            'amount_due' => 12334
        ]);
    }

    public function test_invoice_approved_unpaid_not_credit_note_on_index()
    {
        Order::factory()->count(10)->invoice()->approved()->create();

        $response = $this->json("GET", "/api/orders?with=payments&status=APPROVED&order_type=INV");

        $response->assertStatus(200);

        $response->assertJsonFragment(["total" => 10]);

        $response->assertJsonFragment([
            "status" => "APPROVED",
            "order_type" => "INV"
        ]);
    }
}
