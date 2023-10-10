<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use Tests\AdminBaseTest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Contact;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Setting;
use App\Models\TaxRate;

class BillTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testBillIndex()
    {
        $response = $this->get('/api/orders?order_type=BILL');

        $response->assertStatus(200);
    }

    public function testBillCreateIsHappy()
    {
        $data = Order::factory()->orderLines()->bill()->make()->toArray();
        
        $response = $this->post('/api/orders', $data);

        $response->assertSee('RESOURCE_CREATED');
    }

    public function testBillCreateOrderDateRequired()
    {
        $data = [
            'order_type' => 'BILL',
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
        // dd($response->original);
        $response->assertSee('ORDER_DATE_REQUIRED');
    }

    public function testBillCreateWithStatusApproved()
    {
        $data = [
            'order_type' => 'BILL',
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

    public function testBillCreateWithVoidStatusIsNotAllowed()
    {
        $data = [
            'order_type' => 'BILL',
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

    public function testBillCreateTaxRateMustBeExemptIfTaxSettingIsNoTax()
    {
        $data = [
            'order_type' => 'BILL',
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

    public function testBillUpdate()
    {
        $data = [
            'order_type' => 'BILL',
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

    public function testBillCreateWithOrderLine()
    {
        $data = Order::factory()->orderLines()->bill()->make()->toArray();

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(200);

        $response->assertSee('quantity');

        $response->assertSee('unit_price');

        $response->assertSee('tax_rate');
    }

    public function testBillCreateWithOrderLineWithStatusDraft()
    {
        $data = Order::factory()->orderLines()->bill()->make()->toArray();

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

    public function testBillApprovedDeleteMustBeVoided()
    {
        $data = Order::factory()->orderLines()->approved()->bill()->make([
            'tax_setting' => 'tax inclusive',
            'reference' => 'TEST CREATE'
        ])->toArray();

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $response = $this->delete('/api/orders/' . $data['id']);

        $response->assertStatus(200);

        $response->assertSee('VOID');
    }

    public function testBillForApprovalDeleteMustBeHardDeleted()
    {
        $data = Order::factory()->orderLines()->forApproval()->bill()->make([
            'tax_setting' => 'tax inclusive',
            'reference' => 'TEST CREATE'
        ])->toArray();

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $response = $this->delete('/api/orders/' . $data['id']);

        $response->assertStatus(200);

        $response->assertSee('RESOURCE_TRASHED');
    }

    public function testBillForApprovalDeleteMustBeDeleted()
    {
        $data = Order::factory()->orderLines()->bill()->forApproval()->make([
            'tax_setting' => 'tax inclusive',
            'reference' => 'TEST CREATE'
        ])->toArray();

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['status'] = 'VOID';

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee('VOID');
    }

    public function test_bill_pay()
    {
        $bank = ChartOfAccount::factory()->bankAccount()->create();

        $data = Order::factory()->orderLines()->bill()->make()->toArray();

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

    public function test_bill_credit_note_pay_is_not_allowed()
    {
        $bank = ChartOfAccount::factory()->bankAccount()->create();

        $data = Order::factory()->orderLines()->billCn()->make()->toArray();

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

    public function testBillPayCannotBeHigherThanBalance()
    {
        $data = Order::factory()->orderLines()->bill()->make()->toArray();

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $payment = [
            'amount' => (float) ($data['total_amount'] + 1),
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->create(['type' => 'bank'])->id
        ];

        $response = $this->post("/api/orders/{$data['id']}/pay", $payment);

        $response->assertStatus(422);

        $response->assertSee('PAYMENT_AMOUNT_MUST_BE_LOWER_OR_EQUAL_TO_BALANCE');
    }

    public function testBillPayLowerThanTheBalanceMustNotBePaid()
    {
        $data = Order::factory()->orderLines()->bill()->make()->toArray();

        $total = 0;

        foreach ($data['order_lines'] as $line) {
            $total += $line['amount'];
        }

        $data['total_amount'] = $total;

        $response = $this->post('/api/orders', $data);
        
        $data = $response->original['data']->toArray();

        $payment = [
            'amount' => (float) ($data['total_amount'] - 1),
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->create(['type' => 'bank'])->id
        ];
        
        $response = $this->post("/api/orders/{$data['id']}/pay", $payment);

        $response->assertStatus(200);

        $response->assertSee('PAYMENT_ADDED');

        $response->assertSee('APPROVED');
    }

    public function testBillUpdateDraftToApprovedWithNewOrderLineMustBeHappy()
    {
        $data = [
            'order_type' => 'BILL',
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

        $data['status'] = 'APPROVED';

        $data['order_lines'][] = OrderLine::factory()->make(['tax_rate' => 0, 'tax_rate_id' => 1])->toArray();

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee('RESOURCE_UPDATED');
    }

    public function testBillUpdateDraftToApprovedWithNoOrderLineMustShowOrderLineRequired()
    {
        $data = Order::factory()->bill()->draft()->make([
            'order_lines' => [
                OrderLine::factory()->make(['tax_rate' => 0, 'tax_rate_id' => 1])->toArray()
            ],
        ])->toArray();

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['status'] = 'APPROVED';

        $data['order_lines'] = [];

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(422);

        $response->assertSee('ORDER_LINE_REQUIRED_WHEN_STATUS_IS_NOT_DRAFT');
    }

    public function test_bill_tracked_product_availbable_quantity_must_be_one_after_creating_credit_note()
    {
        $product = Product::factory()->tracked()->create();

        $order = Order::factory()->bill()->approved()->create();

        $tax_rate = TaxRate::factory()->create();

        OrderLine::factory()->create([
            'order_id' => $order['id'],
            'tax_rate' => 0,
            'tax_rate_id' => $tax_rate['id'],
            'product_id' => $product['id'],
            'quantity' => 10
        ]);

        $order2 = Order::factory()->billCn()->approved()->create();

        OrderLine::factory()->create([
            'order_id' => $order2['id'],
            'tax_rate' => 0,
            'tax_rate_id' => $tax_rate['id'],
            'product_id' => $product['id'],
            'quantity' => -9
        ]);
        
        $response = $this->get('/api/products/' . $product['id']);

        $response->assertStatus(200);

        $response->assertJsonFragment(['available_quantity' => 1]);
    }

    public function test_bill_approved_unpaid_not_credit_note_on_index()
    {
        Order::factory()->bill()->approved()->count(3)->create();

        $response = $this->json("GET", "/api/orders?with=payments&status=APPROVED&order_type=BILL");

        $response->assertStatus(200);

        $response->assertJsonFragment(["total" => 3]);

        $response->assertJsonFragment([
            "status" => "APPROVED",
            "order_type" => "BILL"
        ]);
    }

    public function test_bill_create_with_project_is_happy()
    {
        $data = Order::factory()->orderLinesWithProject()->bill()->make()->toArray();
        
        $response = $this->post('/api/orders', $data);

        $response->assertSee('RESOURCE_CREATED');
        
        $this->assertDatabaseHas('expenses', [
            'id' => $response->original['data']->orderLines[0]->expense->id,
        ]);
    }
}
