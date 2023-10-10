<?php

namespace Tests\Unit;

use Tests\BaseTest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Setting;
use App\Models\Contact;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderLine;

class QuoteTest extends BaseTest
{
    use RefreshDatabase;

    public function testQuoteIndex()
    {
        $this->actingAs($this->adminWithPermissions());

        $response = $this->get('/api/orders?order_type=QU');

        $response->assertStatus(200);
    }

    public function testQuoteCreate()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = [
            'order_type' => 'QU',
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

    public function testQuoteCreateWithStatusApproved()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = [
            'order_type' => 'QU',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'SENT',
        ];

        $response = $this->post('/api/orders', $data);

        $response->assertSee('ORDER_LINE_REQUIRED_WHEN_STATUS_IS_NOT_DRAFT');
    }

    public function testQuoteCreateWithVoidStatusIsNotAllowed()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = [
            'order_type' => 'QU',
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => "louie",
            'tax_setting' => "no tax",
            'currency_id' => Currency::factory()->create()->id,
            'status' => 'DELETED',
        ];

        $response = $this->post('/api/orders', $data);

        $response->assertSee('NOT_ALLOWED_TO_CREATE_STATUS_DELETED');
    }

    public function testQuoteCreateTaxRateMustBeExemptIfTaxSettingIsNoTax()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = [
            'order_type' => 'QU',
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

    public function testQuoteUpdate()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = [
            'order_type' => 'QU',
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

        $data['status'] = 'DELETED';

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(422);

        $response->assertSee('UPDATE_STATUS_FROM_DRAFT_TO_DELETED_NOT_ALLOWED');
    }

    public function testQuoteCreateWithOrderLine()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->orderLines()->make(['order_type' => 'QU'])->toArray();

        $data['status'] = 'SENT';

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(200);

        $response->assertSee('quantity');

        $response->assertSee('unit_price');

        $response->assertSee('tax_rate');
    }

    public function testQuoteForApprovalDeleteMustBeVoided()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->orderLines()->make([
            'status' => 'SENT',
            'tax_setting' => 'tax inclusive',
            'order_type' => 'QU',
            'reference' => 'TEST CREATE'
        ])->toArray();

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['status'] = "DELETED";

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee('DELETED');
    }

    public function testQuoteApprovedDeleteMustBeVoided()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

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

    public function testQuoteSentDeleteMustBeVoided()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

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
}
