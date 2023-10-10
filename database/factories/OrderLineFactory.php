<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\Project;
use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderLine>
 */
class OrderLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $unit_price = $this->faker->randomNumber(4);
        $qty = rand(1, 20);
        $discount = 0.15;
        $tax = 0.10;
        return [
            'product_id' => Product::factory()->create()->id,
            'description' => $this->faker->sentence(),
            'quantity' => $qty,
            'unit_price' => $unit_price,
            'discount' => $discount,
            'tax_rate' => $tax,
            'tax_rate_id' => TaxRate::factory()->create(['rate' => 10])->id,
            'chart_of_account_id' => ChartOfAccount::factory()->create([
                'type' => 'asset'
            ])->id,
            'amount' => round($qty * $unit_price * (1-$discount) * (1+$tax), 2)
        ];
    }

    public function noDiscount()
    {
        return $this->state(function (array $attributes) {
            return [
                'discount' => 0.0,
            ];
        });
    }

    public function asset()
    {
        return $this->state(function (array $attributes) {
            return [
                'chart_of_account_id' => ChartOfAccount::factory()->create([
                    'type' => 'current asset'
                ])->id
            ];
        });
    }

    public function project()
    {
        return $this->state(function (array $attributes) {
            return [
                'project_id' => Project::factory()->create()->id
            ];
        });
    }    
    
}
