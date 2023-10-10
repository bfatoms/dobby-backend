<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;

class ProductTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testProductIndexTrackedNonTrackedSoldAndPurchasedIsHappy()
    {
        Product::factory()->create();

        Product::factory()->tracked()->create();

        Product::factory()->purchased()->create();

        Product::factory()->sold()->create();

        $response = $this->get('/api/products');

        $response->assertStatus(200);
    }

    public function testProductShowIsHappy()
    {
        $data = Product::factory()->create();

        $response = $this->get('/api/products/' . $data['id']);

        $response->assertStatus(200);
    }

    public function testProductCreateNonTrackedFromFrontendIsHappy()
    {
        $data = [
            'is_purchased' => false,
            'is_sold' => false,
            'is_tracked' => false,
            'code' => '1234',
            'name' => 'TEST PRODUCT'
        ];

        $response = $this->post('/api/products', $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testProductCreateNonTrackedFromFrontendWithNullValuesIsHappy()
    {
        $data = [
            "is_purchased" => false,
            "is_sold" => false,
            "is_tracked" => false,
            "purchase_price" => null,
            "purchase_account_id" => null,
            "cost_of_goods_sold_account_id" => null,
            "purchase_tax_rate_id" => null,
            "purchase_description" => null,
            "sale_price" => null,
            "sale_account_id" => null,
            "sale_tax_rate_id" => null,
            "sale_description" => null,
            "code" => "c3",
            "name" => "p3"
        ];

        $response = $this->post('/api/products', $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testProductCreateTrackedFromFrontendIsHappy()
    {
        $data = Product::factory()->tracked()->make()->toArray();

        $response = $this->post('/api/products', $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testProductCreateNonTrackedIsHappy()
    {
        $data = Product::factory()->make()->toArray();

        $response = $this->post('/api/products', $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testProductCreateTrackedIsHappy()
    {
        $data = Product::factory()->tracked()->make()->toArray();

        $response = $this->post('/api/products', $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testProductCreateNonTrackedSoldIsHappy()
    {
        $data = Product::factory()->sold()->make()->toArray();

        $response = $this->post('/api/products', $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testProductCreateNonTrackedSoldMin0()
    {
        $data = Product::factory()->sold()->make()->toArray();

        $data['sale_price'] = "-1";

        $response = $this->post('/api/products', $data);

        $response->assertStatus(422);

        $response->assertSee("SALE_PRICE_MIN_0");
    }

    public function testProductCreateNonTrackedPurchasedIsHappy()
    {
        $data = Product::factory()->purchased()->make()->toArray();

        $response = $this->post('/api/products', $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testProductCreateNonTrackedPurchasedMin0()
    {
        $data = Product::factory()->purchased()->make()->toArray();

        $data['purchase_price'] = "-1";

        $response = $this->post('/api/products', $data);

        $response->assertStatus(422);

        $response->assertSee("PURCHASE_PRICE_MIN_0");
    }

    public function testProductCreateNonTrackedPurchasedSoldIsHappy()
    {
        $data = Product::factory()->sold()->purchased()->make()->toArray();

        $response = $this->post('/api/products', $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testProductCreateTrackedRoundedToFourDecimalPlaceUpIsHappy()
    {
        $data = Product::factory()->tracked()->make()->toArray();

        $data['purchase_price'] = 100.544456789;

        $data['sale_price'] = 400.05;

        $response = $this->post('/api/products', $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");

        $response->assertSee("100.5445");

        $response->assertSee("400.05");
    }

    public function testProductCreateTrackedRoundedToFourDecimalPlaceDownIsHappy()
    {
        $data = Product::factory()->tracked()->make()->toArray();

        $data['purchase_price'] = 100.544446789;

        $data['sale_price'] = 400.050345;

        $response = $this->post('/api/products', $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");

        $response->assertSee("100.5444");

        $response->assertSee("400.0503");
    }

    public function testProductCreateTrackedFieldsAreRequired()
    {
        $data = Product::factory()->tracked()->make()->toArray();

        $data['name'] = '';

        $data['code'] = '';

        $data['purchase_price'] = '';

        $data['purchase_tax_rate_id'] = '';

        $data['purchase_account_id'] = '';

        $data['purchase_description'] = '';

        $data['cost_of_goods_sold_account_id'] = '';

        $data['sale_price'] = '';

        $data['sale_account_id'] = '';

        $data['sale_tax_rate_id'] = '';

        $data['sale_description'] = '';

        $data['inventory_asset_account_id'] = '';

        $response = $this->post('/api/products', $data);

        $response->assertStatus(422);

        $response->assertSee("CODE_REQUIRED");

        $response->assertSee("PURCHASE_PRICE_REQUIRED");

        $response->assertSee("PURCHASE_TAX_RATE_REQUIRED");

        $response->assertSee("PURCHASE_DESCRIPTION_REQUIRED");

        $response->assertSee("COST_OF_GOODS_SOLD_ACCOUNT_REQUIRED");

        $response->assertSee("SALE_PRICE_REQUIRED");

        $response->assertSee("SALE_ACCOUNT_REQUIRED");

        $response->assertSee("SALE_TAX_RATE_REQUIRED");

        $response->assertSee("SALE_DESCRIPTION_REQUIRED");

        $response->assertSee("INVENTORY_ASSET_ACCOUNT_IS_REQUIRED");
    }

    public function testProductUpdateNonTrackedIsHappy()
    {
        $data = Product::factory()->create()->toArray();

        $data['name'] = 'TEST_UPDATE';

        $response = $this->put('/api/products/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");

        $response->assertSee("TEST_UPDATE");
    }

    public function testProductUpdateTrackedIsHappy()
    {
        $data = Product::factory()->tracked()->create()->toArray();

        $data['name'] = 'TEST_UPDATE';

        $response = $this->put('/api/products/' . $data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");

        $response->assertSee("TEST_UPDATE");
    }

    public function testProductUpdateNonTrackedSoldIsHappy()
    {
        $data = Product::factory()->sold()->create()->toArray();

        $data2 = Product::factory()->sold()->make()->toArray();

        $response = $this->put('/api/products/' . $data['id'], $data2);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }

    public function testProductUpdateNonTrackedPurchasedIsHappy()
    {
        $data = Product::factory()->purchased()->create()->toArray();

        $data2 = Product::factory()->purchased()->make()->toArray();

        $response = $this->put('/api/products/' . $data['id'], $data2);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }

    public function testProductUpdateNonTrackedPurchasedSoldIsHappy()
    {
        $data = Product::factory()->sold()->purchased()->create()->toArray();

        $data2 = Product::factory()->sold()->purchased()->make()->toArray();

        $response = $this->put('/api/products/' . $data['id'], $data2);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }

    public function testProductTrashIsHappy()
    {
        $data = Product::factory()->create();

        $response = $this->delete('/api/products/' . $data['id']);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");
    }

    public function testProductUpdateNonTrackedPurchasedMin0()
    {
        $data = Product::factory()->purchased()->create()->toArray();

        $data['purchase_price'] = "-1";

        $response = $this->put('/api/products/' . $data['id'], $data);

        $response->assertStatus(422);

        $response->assertSee("PURCHASE_PRICE_MIN_0");
    }

    public function testProductUpdateNonTrackedSoldMin0()
    {
        $data = Product::factory()->sold()->create()->toArray();

        $data['sale_price'] = "-1";

        $response = $this->put('/api/products/' . $data['id'], $data);

        $response->assertStatus(422);

        $response->assertSee("SALE_PRICE_MIN_0");
    }

    public function testProductSearchSales()
    {
        $data = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $response = $this->get("/api/products/search?order_type=SO&search=" . $data['code'], $data);

        $response->assertStatus(200);

        $response->assertSee($data['code']);
    }

    public function testProductSearchInvoice()
    {
        $data = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $response = $this->get("/api/products/search?order_type=INV&search=" . $data['code'], $data);

        $response->assertStatus(200);

        $response->assertSee($data['code']);
    }

    public function testProductSearchInvoiceCreditNote()
    {
        $data = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $response = $this->get("/api/products/search?order_type=INV-CN&search=" . $data['code'], $data);

        $response->assertStatus(200);

        $response->assertSee($data['code']);
    }

    public function testProductSearchInvoiceNoProduct()
    {
        $data = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $response = $this->get("/api/products/search?order_type=TEST&search=test", $data);

        $response->assertStatus(200);

        $response->assertSee('NO_PRODUCTS_FOUND');
    }

    public function testProductSearchPurchases()
    {
        $data = Product::factory()
            ->tracked()
            ->create()
            ->toArray();
        // dd("/api/products/search?order_type=PO&search=" . $data['code'], $data);
        $response = $this->get("/api/products/search?order_type=PO&search={$data['code']}");
        // dd($response);
        $response->assertStatus(200);

        $response->assertSee($data['code']);
    }

    public function testProductSearchBill()
    {
        $data = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $response = $this->get("/api/products/search?order_type=BILL&search=" . $data['code'], $data);

        $response->assertStatus(200);

        $response->assertSee($data['code']);
    }

    public function testProductSearchBillCreditNote()
    {
        $data = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $response = $this->get("/api/products/search?order_type=BILL-CN&search=" . $data['code'], $data);

        $response->assertStatus(200);

        $response->assertSee($data['code']);
    }

    public function testProductSearchReceiveMoney()
    {
        $data = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $response = $this->get("/api/products/search?order_type=RMD&search=" . $data['code'], $data);

        $response->assertStatus(200);

        $response->assertSee($data['code']);
    }

    public function testProductSearchSpendMoney()
    {
        $data = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $response = $this->get("/api/products/search?order_type=SMD&search=" . $data['code'], $data);

        $response->assertStatus(200);

        $response->assertSee($data['code']);
    }

    public function test_in_approved_purchase_order_must_be_0()
    {
        $product = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $response = $this->json('GET', 'api/products/' . $product['id']);

        $response->assertJsonFragment([
            "in_approved_purchase_orders" => 0
        ]);
    }

    public function test_in_approved_sales_orders_must_be_0()
    {
        $product = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $response = $this->json('GET', 'api/products/' . $product['id']);

        $response->assertJsonFragment([
            "in_approved_sales_orders" => 0
        ]);
    }

    public function test_in_approved_quotations_must_be_0()
    {
        $product = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $response = $this->json('GET', 'api/products/' . $product['id']);

        $response->assertJsonFragment([
            "in_approved_quotations" => 0
        ]);
    }

    public function test_in_approved_purchase_order_must_be_1()
    {
        $product = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $order = Order::factory()->purchase()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $response = $this->json('GET', 'api/products/' . $product['id']);

        $response->assertJsonFragment([
            "in_approved_purchase_orders" => 1
        ]);
    }

    public function test_in_approved_sales_orders_must_be_1()
    {
        $product = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $order = Order::factory()->sales()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $response = $this->json('GET', 'api/products/' . $product['id']);

        $response->assertJsonFragment([
            "in_approved_sales_orders" => 1
        ]);
    }

    public function test_in_approved_quotations_must_be_2()
    {
        $product = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $order = Order::factory()->quote()->sent()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $order = Order::factory()->quote()->sent()->create();

        $order->update([
            'status' => 'ACCEPTED'
        ]);

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $response = $this->json('GET', 'api/products/' . $product['id']);

        $response->assertJsonFragment([
            "in_approved_quotations" => 2
        ]);
    }

    public function test_in_approved_must_be_null_when_values_are_false()
    {
        $product = Product::factory()
            ->create()
            ->toArray();

        $order = Order::factory()->quote()->sent()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $order = Order::factory()->quote()->sent()->create();

        $order->update(['status' => 'ACCEPTED']);

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $order = Order::factory()->purchase()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $response = $this->json('GET', 'api/products/' . $product['id']);

        $response->assertJsonFragment([
            "in_approved_quotations" => null,
            "in_approved_sales_orders" => null,
            "in_approved_purchase_orders" => null
        ]);
    }

    public function test_in_approved_must_be_1_when_values_are_1()
    {
        $product = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $order = Order::factory()->quote()->sent()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $order = Order::factory()->sales()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $order = Order::factory()->purchase()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $order = Order::factory()->bill()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 2
        ]);

        $order = Order::factory()->invoice()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $response = $this->json('GET', 'api/products/' . $product['id']);

        $response->assertJsonFragment([
            "in_approved_quotations" => 1,
            "in_approved_sales_orders" => 1,
            "in_approved_purchase_orders" => 1,
            "available_quantity" => 1
        ]);
    }

    public function test_product_transaction_history()
    {
        $this->withoutExceptionHandling();

        $product = Product::factory()
            ->tracked()
            ->create()
            ->toArray();

        $order = Order::factory()->bill()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 2
        ]);

        $order = Order::factory()->invoice()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $order = Order::factory()->billCn()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $order = Order::factory()->invoiceCn()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $order = Order::factory()->spendMoneyDirect()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 2
        ]);

        $order = Order::factory()->receiveMoneyDirect()->approved()->create();

        OrderLine::factory()->create([
            'product_id' => $product['id'],
            'order_id' => $order['id'],
            'quantity' => 1
        ]);

        $response = $this->json('GET', "api/products/{$product['id']}/transaction-history?sortKey=created_at&sortOrder=asc");

        $response->assertStatus(200);

        $response->assertSee("product_id");

        $response->assertSee("chronological_date");
    }

    public function test_store_purchase_account_when_i_purchase_is_on_when_updating_data() 
    {
        $purchase_account = ChartOfAccount::factory()->withTaxRate()->create();

        $product = Product::factory()->purchased()->create()->toArray();

        $product['purchase_account_id'] = $purchase_account['id'];

        $response = $this->json('PUT', '/api/products/'. $product['id'], $product);

        $response->assertJsonFragment([
            'purchase_account_id' => $purchase_account['id'],
            'cost_of_goods_sold_account_id' => null
        ]);
    }

    public function test_store_cogs_when_tracking_is_on_when_updating_data()
    {
        $cogs = ChartOfAccount::factory()->withTaxRate()->create();

        $product = Product::factory()->tracked()->create()->toArray();

        $product['cost_of_goods_sold_account_id'] = $cogs['id'];

        $response = $this->json('PUT', '/api/products/'. $product['id'], $product);

        $response->assertJsonFragment([
            'purchase_account_id' => null,
            'cost_of_goods_sold_account_id' => $cogs['id']
        ]);
    }

    public function test_when_updating_product_from_purchased_to_tracked_it_should_set_purchase_account_id_to_null()
    {
        $cogs = ChartOfAccount::factory()->withTaxRate()->create();

        $inventory = ChartOfAccount::factory()->withTaxRate()->create([
            'type' => 'inventory'
        ]);

        $product = Product::factory()->purchased()->create()->toArray();

        $product['cost_of_goods_sold_account_id'] = $cogs['id'];

        $product['inventory_asset_account_id'] = $inventory['id'];

        $product['is_purchased'] = false;
        
        $product['is_tracked'] = false;

        $response = $this->json('PUT', '/api/products/'. $product['id'], $product);

        $response->assertJsonFragment([
            'purchase_account_id' => null,
            'cost_of_goods_sold_account_id' => $cogs['id']
        ]);
    }
}
