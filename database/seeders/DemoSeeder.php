<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Contact;
use App\Models\Product;
use App\Models\TaxRate;
use Illuminate\Database\Seeder;


class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') != 'production' && config('app.env') != 'testing') {
            Setting::factory()->create();

            $tax_rate = TaxRate::first();

            $currency = Currency::first();

            $inventory = ChartOfAccount::where('code', '999999')->first();

            if (empty($inventory)) {
                $inventory = ChartOfAccount::factory()->create([
                    'description' => 'The Default Inventory Account for the Demo Seeder',
                    'name' => 'Inventory Account',
                    'code' => '999999',
                    'tax_rate_id' => $tax_rate['id'],
                    'type' => 'inventory',
                    'is_system_account' => false,
                ]);    
            }

            $cogs = ChartOfAccount::where('code', '777777')->first();

            if (empty($cogs)) {
                $cogs = ChartOfAccount::factory()->create([
                    'description' => 'Cost of Goods Sold Account',
                    'name' => 'Cost of Goods Sold',
                    'code' => '777777',
                    'tax_rate_id' => $tax_rate['id'],
                    'type' => 'expense',
                    'is_system_account' => false,
                ]);
            }

            $purchase_account = ChartOfAccount::where('code', '777770')->first();

            if(empty($purchase_account)) {
                $purchase_account = ChartOfAccount::factory()->create([
                    'description' => 'Purchase Account',
                    'name' => 'Purchase Account',
                    'code' => '777770',
                    'tax_rate_id' => $tax_rate['id'],
                    'type' => 'expense',
                    'is_system_account' => false,
                ]);    
            }

            $sales_account = ChartOfAccount::where('code', '600800')->first();

            if(empty($sales_account)){
                $sales_account = ChartOfAccount::factory()->create([
                    'description' => 'Sales Account',
                    'name' => 'Sales Account',
                    'code' => '600800',
                    'tax_rate_id' => $tax_rate['id'],
                    'type' => 'revenue',
                    'is_system_account' => false,
                ]);    
            }

            $bank_account = ChartOfAccount::where('code', '123456789')->first();

            if(empty($bank_account)) {
                $bank_account = ChartOfAccount::factory()->create([
                    'description' => 'Peso Bank Account',
                    'name' => 'PESO BANK ACCOUNT',
                    'code' => '123456789',
                    'tax_rate_id' => $tax_rate['id'],
                    'type' => 'bank',
                    'is_system_account' => false,
                    'currency_id' => $currency['id'],
                    'bank_name' => 'Peso Bank Account'
                ]);    
            }

            $contact = Contact::where('name', 'Supplier Manny')->first();

            if(empty($contact)){
                $contact = Contact::factory()->create([
                    'purchase_account_id' => $purchase_account['id'],
                    'purchase_tax_rate_id' => $tax_rate['id'],
                    'sale_tax_rate_id' => $tax_rate['id'],
                    'sale_account_id' => $sales_account['id'],
                    'sale_tax_type' => 'no tax',
                    'purchase_tax_type' => 'no tax',
                    'sale_discount' => 0,
                    'name' => 'Supplier Manny'
                ]);    
            }

            $contact = Contact::where('name', 'Customer Manny')->first();

            if(empty($contact)){
                $contact = Contact::factory()->create([
                    'purchase_account_id' => $purchase_account['id'],
                    'purchase_tax_rate_id' => $tax_rate['id'],
                    'sale_tax_rate_id' => $tax_rate['id'],
                    'sale_account_id' => $sales_account['id'],
                    'sale_tax_type' => 'no tax',
                    'purchase_tax_type' => 'no tax',
                    'sale_discount' => 0,
                    'name' => 'Customer Manny'
                ]);    
            }

            $unknown_product = Product::where('code', '00000')->first();

            if(empty($unknown_product)){
                $unknown_product = Product::factory()->create([
                    'name' => 'Ex-Deals',
                    'code' => '00000'
                ]);    
            }

            $sold = Product::where('code', '00001')->first();

            if(empty($sold)){
                $sold = Product::factory()->blankSold()->create([
                    'sale_account_id' => $sales_account['id'],
                    'sale_tax_rate_id' => $tax_rate['id'],
                    'name' => 'Software Development Sold Only',
                    'code' => '00001'
                ]);                    
            }
            
            $purchased = Product::where('code', '00002')->first();
            
            if(empty($purchased)){
                $purchased = Product::factory()->blankPurchased()->create([
                    'purchase_account_id' => $purchase_account['id'],
                    'purchase_tax_rate_id' => $tax_rate['id'],
                    'name' => 'Test Phones Purchased Only',
                    'code' => '00002'
                ]);    
            }

            $sold_purchased = Product::where('code', '00003')->first();

            if(empty($sold_purchased)){
                $sold_purchased = Product::factory()->blankSold()->blankPurchased()->create([
                    'sale_account_id' => $sales_account['id'],
                    'sale_tax_rate_id' => $tax_rate['id'],        
                    'purchase_account_id' => $purchase_account['id'],
                    'purchase_tax_rate_id' => $tax_rate['id'],
                    'name' => 'Unknown Product Sold and Purchased',
                    'code' => '00003'
                ]);    
            }

            $tracked = Product::where('code', '00004')->first();

            if(empty($tracked)){
                $tracked = Product::factory()->blankTracked()->create([
                    'sale_account_id' => $sales_account['id'],
                    'sale_tax_rate_id' => $tax_rate['id'],        
                    'cost_of_goods_sold_account_id' => $cogs['id'],
                    'purchase_tax_rate_id' => $tax_rate['id'],
                    'inventory_asset_account_id' => $inventory['id'],
                    'name' => 'Manila Styles Tracked Inventory',
                    'code' => '00004'
                ]);    
            }
        }
    }
}
