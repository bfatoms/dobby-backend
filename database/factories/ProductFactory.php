<?php

namespace Database\Factories;

use App\Models\TaxRate;
use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'code' => $this->faker->randomNumber(5, false),
            'name' => $this->faker->name,
            'is_tracked' => false,
            'is_sold' => false,
            'is_purchased' => false,    
        ];
    }

    public function tracked()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_tracked' => true,
                'inventory_asset_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id,
                'is_purchased' => true,
                'purchase_price' => $this->faker->randomNumber(2),
                'purchase_tax_rate_id' => TaxRate::factory()->create()->id,
                'purchase_description' => $this->faker->sentence,
                'purchase_account_id' => null,
                'cost_of_goods_sold_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id,
                'is_sold' => true,
                'sale_price' => $this->faker->randomNumber(2),
                'sale_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id,
                'sale_tax_rate_id' => TaxRate::factory()->create()->id,
                'sale_description' => $this->faker->sentence,
            ];
        });
    }

    public function purchased()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_purchased' => true,
                'purchase_price' => $this->faker->randomNumber(2),
                'purchase_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id,
                'purchase_tax_rate_id' => TaxRate::factory()->create()->id,
                'purchase_description' => $this->faker->sentence,
                'is_tracked' => false,
                'is_sold' => false,
            ];
        });
    }

    public function sold()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_sold' => true,
                'sale_price' => $this->faker->randomNumber(2),
                'sale_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id,
                'sale_tax_rate_id' => TaxRate::factory()->create()->id,
                'sale_description' => $this->faker->sentence,
                'is_purchased' => false,
                'is_tracked' => false,
            ];
        });
    }

    public function blankSold()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_sold' => true,
                'sale_price' => $this->faker->randomNumber(2),
                'sale_description' => $this->faker->sentence,
                'is_purchased' => false,
                'is_tracked' => false
            ];
        });
    }

    public function blankPurchased()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_purchased' => true,
                'purchase_price' => $this->faker->randomNumber(2),
                'purchase_description' => $this->faker->sentence,
                'is_tracked' => false,
                'is_sold' => false
            ];
        });
    }

    public function blankTracked()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_tracked' => true,
                'is_purchased' => true,
                'purchase_price' => $this->faker->randomNumber(2),
                'purchase_description' => $this->faker->sentence,
                'is_sold' => true,
                'sale_price' => $this->faker->randomNumber(2),
                'sale_description' => $this->faker->sentence,
            ];
        });
    }
}


