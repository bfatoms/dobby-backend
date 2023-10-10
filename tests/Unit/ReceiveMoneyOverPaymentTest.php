<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;

class ReceiveMoneyOverPaymentTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testReceiveMoneyOverPaymentIndex()
    {
        $response = $this->get('/api/orders?order_type=RMO');

        $response->assertStatus(200);
    }

    public function testReceiveMoneyOverPaymentCreate()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $data = Order::factory()->receiveMoneyOverpayment()
            ->make([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id'],
                'order_lines' => [
                    OrderLine::factory()->make([
                        'product_id' => "",
                        "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-payable')->first()->id
                    ])->toArray()
                ]
            ])
            ->toArray();

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(200);

        $response->assertSee($data['order_type']);
    }

    public function testReceiveMoneyOverPaymentSameCurrencyCreate()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $data = Order::factory()->receiveMoneyOverpayment()
            ->make([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id'],
                'order_lines' => [
                    OrderLine::factory()->make([
                        'product_id' => "",
                        "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-payable')->first()->id
                    ])->toArray()
                ]
            ])
            ->toArray();

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(200);

        $response->assertSee($data['order_type']);
    }

    public function testReceiveMoneyOverPaymentDraftCreate()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $data = Order::factory()->receiveMoneyOverpayment()->draft()
            ->make([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id'],
                'order_lines' => [
                    OrderLine::factory()->make([
                        'product_id' => "",
                        "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-payable')->first()->id
                    ])->toArray()
                ]
            ])
            ->toArray();

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(422);

        $response->assertSee("NOT_ALLOWED_TO_CREATE_STATUS_DRAFT");
    }

    public function testReceiveMoneyOverPaymentUpdateIsHappy()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $order = Order::factory()->receiveMoneyOverpayment()
            ->create([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id']
            ]);
        
        $order_lines = OrderLine::factory()->create([
            'order_id' => $order['id'],
            'product_id' => null,
            "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-payable')
                ->first()->id
        ]);

        $order = $order->toArray();
        
        $order['reference'] = 'TEST REFERENCE';

        $order['order_lines'] = [$order_lines->toArray()];
        
        $response = $this->put('/api/orders/' . $order['id'], $order);
        
        $response->assertStatus(200);
        
        $response->assertSee('TEST REFERENCE');
    }

    public function testReceiveMoneyOverPaymentUpdateToVoid()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $order = Order::factory()->receiveMoneyOverpayment()
            ->create([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id']
            ]);
        
        $order_lines = OrderLine::factory()->create([
            'order_id' => $order['id'],
            'product_id' => null,
            "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-payable')
                ->first()->id
        ]);

        $order = $order->toArray();
        
        $order['status'] = 'VOID';

        $order['order_lines'] = [$order_lines->toArray()];
        
        $response = $this->put('/api/orders/' . $order['id'], $order);

        $response->assertStatus(200);

        $response->assertSee("VOID");

        $response->assertSee('RMO');
    }

    public function testReceiveMoneyOverPaymentUpdateToPaid()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $order = Order::factory()->receiveMoneyOverpayment()
            ->create([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id']
            ]);
        
        $order_lines = OrderLine::factory()->create([
            'order_id' => $order['id'],
            'product_id' => null,
            "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-payable')
                ->first()->id
        ]);

        $order = $order->toArray();
        
        $order['status'] = 'PAID';

        $order['order_lines'] = [$order_lines->toArray()];
        
        $response = $this->put('/api/orders/' . $order['id'], $order);

        $response->assertStatus(200);

        $response->assertSee("PAID");

        $response->assertSee('RMO');
    }

    public function testReceiveMoneyOverPaymentCreateToPaidProductMustBeEmpty()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $product = Product::factory()->create();

        $order = Order::factory()->receiveMoneyOverpayment()
            ->make([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id'],
                'order_lines' => [
                    OrderLine::factory()->make([
                        'product_id' => $product['id'],
                        "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-payable')
                            ->first()->id
                    ])->toArray()
                ]
            ])->toArray();
        
        $response = $this->post('/api/orders', $order);

        $response->assertStatus(422);

        $response->assertSeeText("PRODUCT_MUST_BE_NULL_OR_EMPTY");
    }

    public function testReceiveMoneyOverPaymentUpdateToDraft()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $order = Order::factory()->receiveMoneyOverpayment()
            ->create([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id']
            ]);
        
        $order_lines = OrderLine::factory()->create([
            'order_id' => $order['id'],
            'product_id' => null,
            "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-payable')
                ->first()->id
        ]);

        $order = $order->toArray();
        
        $order['status'] = 'DRAFT';

        $order['order_lines'] = [$order_lines->toArray()];
        
        $response = $this->put('/api/orders/' . $order['id'], $order);


        $response->assertStatus(422);

        $response->assertSee("NOT_ALLOWED_TO_CREATE_STATUS_DRAFT");
    }

    public function testReceiveMoneyOverPaymentDelete()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $order = Order::factory()->receiveMoneyOverpayment()
            ->create([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id']
            ]);
        
        OrderLine::factory()->create([
            'order_id' => $order['id'],
            'product_id' => null,
            "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-payable')
                ->first()->id
        ]);

        $response = $this->delete('/api/orders/' . $order['id']);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");

        $response->assertSee("VOID");
    }
}
