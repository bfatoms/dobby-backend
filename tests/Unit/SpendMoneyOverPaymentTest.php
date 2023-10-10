<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;
use App\Models\OrderLine;
use App\Models\Product;

class SpendMoneyOverPaymentTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testSpendMoneyOverPaymentIndex()
    {
        $response = $this->get('/api/orders?order_type=SMO');

        $response->assertStatus(200);
    }

    public function testSpendMoneyOverPaymentCreate()
    {
        $account = ChartOfAccount::factory()->spendMoney()->create();

        $data = Order::factory()->spendMoneyOverpayment()
            ->make([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id'],
                'order_lines' => [
                    OrderLine::factory()->make([
                        'product_id' => "",
                        "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-receivable')->first()->id
                    ])->toArray()
                ]
            ])
            ->toArray();

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(200);

        $response->assertSee($data['order_type']);
    }

    public function testSpendMoneyOverPaymentSameCurrencyCreate()
    {
        $account = ChartOfAccount::factory()->spendMoney()->create();

        $data = Order::factory()->spendMoneyOverpayment()
            ->make([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id'],
                'order_lines' => [
                    OrderLine::factory()->make([
                        'product_id' => "",
                        "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-receivable')->first()->id
                    ])->toArray()
                ]
            ])
            ->toArray();

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(200);

        $response->assertSee($data['order_type']);
    }

    public function testSpendMoneyOverPaymentDraftCreate()
    {
        $account = ChartOfAccount::factory()->spendMoney()->create();

        $data = Order::factory()->spendMoneyOverpayment()->draft()
            ->make([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id'],
                'order_lines' => [
                    OrderLine::factory()->make([
                        'product_id' => "",
                        "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-receivable')->first()->id
                    ])->toArray()
                ]
            ])
            ->toArray();

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(422);

        $response->assertSee("NOT_ALLOWED_TO_CREATE_STATUS_DRAFT");
    }

    public function testSpendMoneyOverPaymentUpdateIsHappy()
    {
        $account = ChartOfAccount::factory()->spendMoney()->create();

        $order = Order::factory()->spendMoneyOverpayment()
            ->create([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id']
            ]);
        
        $order_lines = OrderLine::factory()->create([
            'order_id' => $order['id'],
            'product_id' => null,
            "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-receivable')
                ->first()->id
        ]);

        $order = $order->toArray();
        
        $order['reference'] = 'TEST REFERENCE';

        $order['order_lines'] = [$order_lines->toArray()];
        
        $response = $this->put('/api/orders/' . $order['id'], $order);
        
        $response->assertStatus(200);
        
        $response->assertSee('TEST REFERENCE');
    }

    public function testSpendMoneyOverPaymentUpdateToVoid()
    {
        $account = ChartOfAccount::factory()->spendMoney()->create();

        $order = Order::factory()->spendMoneyOverpayment()
            ->create([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id']
            ]);
        
        $order_lines = OrderLine::factory()->create([
            'order_id' => $order['id'],
            'product_id' => null,
            "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-receivable')
                ->first()->id
        ]);

        $order = $order->toArray();
        
        $order['status'] = 'VOID';

        $order['order_lines'] = [$order_lines->toArray()];
        
        $response = $this->put('/api/orders/' . $order['id'], $order);

        $response->assertStatus(200);

        $response->assertSee("VOID");

        $response->assertSee('SMO');
    }

    public function testSpendMoneyOverPaymentUpdateToPaid()
    {
        $account = ChartOfAccount::factory()->spendMoney()->create();

        $order = Order::factory()->spendMoneyOverpayment()
            ->create([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id']
            ]);
        
        $order_lines = OrderLine::factory()->create([
            'order_id' => $order['id'],
            'product_id' => null,
            "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-receivable')
                ->first()->id
        ]);

        $order = $order->toArray();
        
        $order['status'] = 'PAID';

        $order['order_lines'] = [$order_lines->toArray()];
        
        $response = $this->put('/api/orders/' . $order['id'], $order);

        $response->assertStatus(200);

        $response->assertSee("PAID");

        $response->assertSee('SMO');
    }

    public function testSpendMoneyOverPaymentCreateToPaidProductMustBeEmpty()
    {
        $account = ChartOfAccount::factory()->spendMoney()->create();

        $product = Product::factory()->create();

        $order = Order::factory()->spendMoneyOverpayment()
            ->make([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id'],
                'order_lines' => [
                    OrderLine::factory()->make([
                        'product_id' => $product['id'],
                        "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-receivable')
                            ->first()->id
                    ])->toArray()
                ]
            ])->toArray();
        
        $response = $this->post('/api/orders', $order);

        $response->assertStatus(422);

        $response->assertSeeText("PRODUCT_MUST_BE_NULL_OR_EMPTY");
    }

    public function testSpendMoneyOverPaymentUpdateToDraft()
    {
        $account = ChartOfAccount::factory()->spendMoney()->create();

        $order = Order::factory()->spendMoneyOverpayment()
            ->create([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id']
            ]);
        
        $order_lines = OrderLine::factory()->create([
            'order_id' => $order['id'],
            'product_id' => null,
            "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-receivable')
                ->first()->id
        ]);

        $order = $order->toArray();
        
        $order['status'] = 'DRAFT';

        $order['order_lines'] = [$order_lines->toArray()];
        
        $response = $this->put('/api/orders/' . $order['id'], $order);


        $response->assertStatus(422);

        $response->assertSee("NOT_ALLOWED_TO_CREATE_STATUS_DRAFT");
    }

    public function testSpendMoneyOverPaymentDelete()
    {
        $account = ChartOfAccount::factory()->spendMoney()->create();

        $order = Order::factory()->spendMoneyOverpayment()
            ->create([
                'currency_id' => $this->setting['currency_id'],
                'bank_id' => $account['id']
            ]);
        
        OrderLine::factory()->create([
            'order_id' => $order['id'],
            'product_id' => null,
            "chart_of_account_id" => ChartOfAccount::where('system_name', 'accounts-receivable')
                ->first()->id
        ]);

        $response = $this->delete('/api/orders/' . $order['id']);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");

        $response->assertSee("VOID");
    }
}
