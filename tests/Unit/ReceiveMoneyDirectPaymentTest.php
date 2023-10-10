<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BaseTest;

class ReceiveMoneyDirectPaymentTest extends BaseTest
{
    use RefreshDatabase;

    public function testReceiveMoneyDirectPaymentIndex()
    {
        $this->actingAs($this->adminWithPermissions());

        $response = $this->get('/api/orders?order_type=RMD');

        $response->assertStatus(200);
    }

    public function testReceiveMoneyDirectPaymentCreate()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->receiveMoneyDirect()->orderLines()->make()->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->receiveMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(200);

        $response->assertSee($data['order_type']);
    }

    public function testReceiveMoneyDirectPaymentCreateSameCurrency()
    {
        $this->actingAs($this->adminWithPermissions());

        $settings = Setting::factory()->create();

        $data = Order::factory()->receiveMoneyDirect()->orderLines()
            ->make(['currency_id' => $settings['currency_id']])->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->receiveMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(200);

        $response->assertSee($data['order_type']);
    }

    public function testReceiveMoneyDirectPaymentDraftCreate()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->receiveMoneyDirect()->orderLines()->make()->toArray();

        $data['status'] = "DRAFT";

        $data['bank_id'] = ChartOfAccount::factory()->receiveMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(422);

        $response->assertSee("NOT_ALLOWED_TO_CREATE_STATUS_DRAFT");
    }

    public function testReceiveMoneyDirectPaymentUpdate()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->receiveMoneyDirect()->orderLines()->make()->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->receiveMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee("RM");
    }

    public function testReceiveMoneyDirectPaymentUpdateToVoid()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->receiveMoneyDirect()->orderLines()->make()->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->receiveMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['status'] = 'VOID';

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee("VOID");

        $response->assertSee("RM");
    }

    public function testReceiveMoneyDirectPaymentUpdateToPaid()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->receiveMoneyDirect()->orderLines()->make()->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->receiveMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['status'] = 'PAID';

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee("PAID");

        $response->assertSee("RM");
    }

    public function testReceiveMoneyDirectPaymentUpdateToDraft()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->receiveMoneyDirect()->orderLines()->make()->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->receiveMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $data['status'] = 'DRAFT';

        $response = $this->put('/api/orders/' . $data['id'], $data);

        $response->assertStatus(422);

        $response->assertSee("NOT_ALLOWED_TO_CREATE_STATUS_DRAFT");
    }

    public function testReceiveMoneyDirectPaymentDelete()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->receiveMoneyDirect()->orderLines()->make()->toArray();

        $data['bank_id'] = ChartOfAccount::factory()->receiveMoney()->create()->id;

        $response = $this->post('/api/orders', $data);

        $data = $response->original['data']->toArray();

        $response = $this->delete('/api/orders/' . $data['id']);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");

        $response->assertSee("VOID");
    }
}
