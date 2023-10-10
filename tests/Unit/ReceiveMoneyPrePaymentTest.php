<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;
use App\Models\OrderLine;
use App\Models\Product;

class ReceiveMoneyPrePaymentTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testReceiveMoneyPrePaymentIndex()
    {
        $response = $this->get('/api/orders?order_type=RMP');

        $response->assertStatus(200);
    }

    public function testReceiveMoneyPrePaymentCreate()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $data = Order::factory()->receiveMoneyPrepayment()
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

    public function testReceiveMoneyPrePaymentSameCurrencyCreate()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $data = Order::factory()->receiveMoneyPrepayment()
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

    public function testReceiveMoneyPrePaymentDraftCreate()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $data = Order::factory()->receiveMoneyPrepayment()->draft()
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

    public function testReceiveMoneyPrePaymentUpdateIsHappy()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $order = Order::factory()->receiveMoneyPrepayment()
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

    public function testReceiveMoneyPrePaymentUpdateToVoid()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $order = Order::factory()->receiveMoneyPrepayment()
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

        $response->assertSee('RMP');
    }

    public function testReceiveMoneyPrePaymentUpdateToPaid()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $order = Order::factory()->receiveMoneyPrepayment()
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

        $response->assertSee('RMP');
    }

    public function testReceiveMoneyPrePaymentCreateToPaidProductMustBeEmpty()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $product = Product::factory()->create();

        $order = Order::factory()->receiveMoneyPrepayment()
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

    public function testReceiveMoneyPrePaymentUpdateToDraft()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $order = Order::factory()->receiveMoneyPrepayment()
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

    public function testReceiveMoneyPrePaymentDelete()
    {
        $account = ChartOfAccount::factory()->receiveMoney()->create();

        $order = Order::factory()->receiveMoneyPrepayment()
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
