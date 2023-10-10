<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "code" => $this->faker->unique()->regexify('[A-Z0-9a-z]{3}'),
            "symbol" => $this->faker->unique()->regexify('[A-Z]'),
            "name" => $this->faker->name,
        ];
    }
}
