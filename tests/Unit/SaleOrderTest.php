<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BaseTest;
use App\Models\Setting;
use App\Models\Contact;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;

class SaleOrderTest extends BaseTest
{
    use RefreshDatabase;

    public function testSalesOrderIndex()
    {
        $this->actingAs($this->adminWithPermissions());

        $response = $this->get('/api/orders');

        $response->assertStatus(200);
    }

    public function testSalesOrderCreateTotal()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = [
            'order_type' => 'SO',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'DRAFT',
        ];

        $response = $this->post('/api/orders', $data);

        $response->assertSee('RESOURCE_CREATED');

        $response = $this->get('/api/orders?all=true');

        $response->assertSee('total');
    }

    public function testSalesOrderCreateTotalShow()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = [
            'order_type' => 'SO',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'DRAFT',
        ];

        $response = $this->post('/api/orders', $data);

        $response->assertSee('RESOURCE_CREATED');

        $data = $response->original['data']->toArray();
        
        $response = $this->get('/api/orders/'. $data['id']);

        $response->assertSee('total');
    }

    public function testSalesOrderCreate()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = [
            'order_type' => 'SO',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'DRAFT',
        ];

        $response = $this->post('/api/orders', $data);

        $response->assertSee('RESOURCE_CREATED');
    }

    public function testSalesOrderCreateWithStatusApproved()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = [
            'order_type' => 'SO',
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

    public function testSalesOrderCreateWithVoidStatusIsNotAllowed()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = [
            'order_type' => 'SO',
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

    public function testSalesOrderCreateTaxRateMustBeExemptIfTaxSettingIsNoTax()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = [
            'order_type' => 'SO',
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

    public function testSalesOrderUpdate()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = [
            'order_type' => 'SO',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'DRAFT',
            'order_lines' => [
                OrderLine::factory()->make(['tax_rate' => 0, 'tax_rate_id' => 1])->toArray()
            ]
        ];

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['status'] = 'VOID';

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(422);

        $response->assertSee('UPDATE_STATUS_FROM_DRAFT_TO_VOID_NOT_ALLOWED');
    }

    public function testSalesOrderCreateWithOrderLine()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->orderLines()->make(['order_type' => 'SO'])->toArray();

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(200);

        $response->assertSee('quantity');

        $response->assertSee('unit_price');

        $response->assertSee('tax_rate');
    }

    public function testSalesOrderCreateWithOrderLineWithStatusDraft()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->sales()->make()->toArray();

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

    public function testSalesOrderUpdateDraftDeleteOneOrderLine()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->orderLines()->make([
            'status' => 'DRAFT',
            'tax_setting' => 'tax inclusive',
            'order_type' => 'SO'
        ])->toArray();

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $to_remove_id = $data['order_lines'][0]['id'];

        $data['order_lines'] = array_splice($data['order_lines'], 1);

        $response->assertSee($data['order_lines'][0]['id']);

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertDontSee($to_remove_id);
    }

    public function testSalesOrderUpdateApprovedToInvoicedWithOrderLineMustNotUpdateAnyFieldExceptStatus()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->orderLines()->approved()->sales()->make([
            'tax_setting' => 'tax inclusive',
            'reference' => 'TEST CREATE'
        ])->toArray();

        $response = $this->post('/api/orders', $data);
        
        $data = $response->original['data']->toArray();

        $new_product = Product::factory()->create();

        $data['order_lines'][] = OrderLine::factory()
            ->make(['product_id' => $new_product['id']])
            ->toArray(); 

        $data['status'] = 'INVOICED';

        $data['reference'] = 'TEST UPDATE';

        $response->assertSee($data['order_lines'][0]['id']);
        
        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertDontSee($new_product['id']);

        $response->assertSee('INVOICED');

        $response->assertSee('RESOURCE_UPDATED');
    }

    public function testSalesOrderForApprovalDeleteMustBeDeleted()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->orderLines()->forApproval()->sales()->make([
            'tax_setting' => 'tax inclusive',
            'reference' => 'TEST CREATE'
        ])->toArray();

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $response = $this->delete('/api/orders/'. $data['id']);

        $response->assertStatus(200);

        $response->assertSee('DELETED');
    }

    public function testSalesOrderApprovedDeleteMustBeDeleted()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->orderLines()->approved()->sales()->make([
            'tax_setting' => 'tax inclusive',
            'reference' => 'TEST CREATE'
        ])->toArray();

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $response = $this->delete('/api/orders/'. $data['id']);

        $response->assertStatus(200);

        $response->assertSee('DELETED');
    }
}
