<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tax_rate = TaxRate::find(1);
        if (empty($tax_rate)) {
            $tax = config('accounting.default_tax');
            TaxRate::create($tax[0]);
        }
    }
}
