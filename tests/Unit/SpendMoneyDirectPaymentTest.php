<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BaseTest;

class SpendMoneyDirectPaymentTest extends BaseTest
{
    use RefreshDatabase;

    public function testSpendMoneyDirectPaymentIndex()
    {
        $this->actingAs($this->adminWithPermissions());

        $response = $this->get('/api/orders?order_type=SMD');

        $response->assertStatus(200);
    }

    public function testSpendMoneyDirectPaymentCreate()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->spendMoneyDirect()->orderLines()->make()->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->spendMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(200);

        $response->assertSee($data['order_type']);
    }

    public function testSpendMoneyDirectPaymentCreateSameCurrency()
    {
        $this->actingAs($this->adminWithPermissions());

        $settings = Setting::factory()->create();

        $data = Order::factory()->spendMoneyDirect()->orderLines()
            ->make(['currency_id' => $settings['currency_id']])->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->spendMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(200);

        $response->assertSee($data['order_type']);
    }

    public function testSpendMoneyDirectPaymentDraftCreate()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->spendMoneyDirect()->orderLines()->make()->toArray();

        $data['status'] = "DRAFT";

        $data['bank_id'] = ChartOfAccount::factory()->spendMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(422);

        $response->assertSee("NOT_ALLOWED_TO_CREATE_STATUS_DRAFT");
    }

    public function testSpendMoneyDirectPaymentUpdate()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->spendMoneyDirect()->orderLines()->make()->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->spendMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee("SM");
    }

    public function testSpendMoneyDirectPaymentUpdateToVoid()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->spendMoneyDirect()->orderLines()->make()->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->spendMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['status'] = 'VOID';

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee("VOID");

        $response->assertSee("SMD");
    }

    public function testSpendMoneyDirectPaymentUpdateToPaid()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->spendMoneyDirect()->orderLines()->make()->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->spendMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['status'] = 'PAID';

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee("PAID");

        $response->assertSee("SMD");
    }

    public function testSpendMoneyDirectPaymentUpdateToDraft()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->spendMoneyDirect()->orderLines()->make()->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->spendMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['status'] = 'DRAFT';

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(422);

        $response->assertSee("NOT_ALLOWED_TO_CREATE_STATUS_DRAFT");
    }

    public function testSpendMoneyDirectPaymentDelete()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->spendMoneyDirect()->orderLines()->make()->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->spendMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $response = $this->delete('/api/orders/' . $data['id']);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");

        $response->assertSee("VOID");
    }
}
