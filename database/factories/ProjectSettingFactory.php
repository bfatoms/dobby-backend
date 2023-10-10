<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectSetting>
 */
class ProjectSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'product_id' => Product::factory()->create()->id,
            'sales_price' => $this->faker->randomNumber(7),
            'purchase_price' => $this->faker->randomNumber(7),
    
        ];
    }
}
