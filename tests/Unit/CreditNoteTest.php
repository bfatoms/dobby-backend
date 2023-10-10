<?php

namespace Tests\Unit;

use Tests\BaseTest;
use App\Models\Setting;
use App\Models\Contact;
use App\Models\Currency;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreditNoteTest extends BaseTest
{
    use RefreshDatabase;

    public function testInvoiceCreditNoteCreate()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $data = Order::factory()->orderLines()->make(['order_type' => 'INV-CN'])->toArray();

        $response = $this->post('/api/orders', $data);

        $response->assertSee('RESOURCE_CREATED');

        $response->assertSee('INV-CN');
    }
}
