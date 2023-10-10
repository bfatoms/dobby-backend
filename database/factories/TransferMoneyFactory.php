<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransferMoney>
 */
class TransferMoneyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'from_bank_account_id' => ChartOfAccount::factory()->bankAccount()->create()->id,
            'to_bank_account_id' => ChartOfAccount::factory()->bankAccount()->create()->id,
            'from_amount' => 1.00,
            'to_amount' => 1.00,
        ];
    }
}
