<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaxRate>
 */
class TaxRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "name" => $this->faker->name,
            "rate" => 10,    
        ];
    }

    public function noTax()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Tax Exempt',
                'rate' => 0.0,
                'is_system_tax' => true
            ];
        });
    }
}
