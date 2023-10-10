<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = config('accounting.default_account');

        $tax = TaxRate::where('is_system_tax', true)->where('rate', 0.0)->first();
        
        $receivable = ChartOfAccount::find(1);

        if (empty($receivable)) {
            ChartOfAccount::create(array_merge($accounts[0], ['tax_rate_id' => $tax['id']]));
        }

        $payable = ChartOfAccount::find(2);

        if (empty($payable)) {
            ChartOfAccount::create(array_merge($accounts[1], ['tax_rate_id' => $tax['id']]));
        }

        $sales_tax = ChartOfAccount::find(3);

        if (empty($sales_tax)) {
            ChartOfAccount::create(array_merge($accounts[2], ['tax_rate_id' => $tax['id']]));
        }
    }
}
