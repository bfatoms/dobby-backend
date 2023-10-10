<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'paid_at' => now()->toDateTimeString(),
            'chart_of_account_id' => ChartOfAccount::factory()->withTaxRate()->create()->id,
            'reference' => $this->faker->sentence(1),
            'exchange_rate' => 1,
        ];

    }
}
