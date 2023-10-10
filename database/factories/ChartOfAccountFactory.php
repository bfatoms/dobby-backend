<?php

namespace Database\Factories;

use App\Models\TaxRate;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChartOfAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => rand(100000, 9999999),
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'type' => 'liabilities',    
        ];
    }

    public function withTaxRate()
    {
        return $this->state(function (array $attributes) {
            return [
                'tax_rate_id' => TaxRate::factory()->create()->id,
            ];
        });
    }

    public function receiveMoney()
    {
        return $this->state(function (array $attributes) {
            return [
                'currency_id' => Setting::first()->currency_id,
            ];
        });
    }

    public function spendMoney()
    {
        return $this->state(function (array $attributes) {
            return [
                'currency_id' => Setting::first()->currency_id,
            ];
        });
    }


    public function bankAccount()
    {
        return $this->state(function (array $attributes) {
            return [
                'currency_id' => Setting::first()->currency_id,
                'type' => 'bank',
                'bank_name' => 'Louie Maya Bank Peso',
            ];
        });
    }
}
