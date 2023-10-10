<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use App\Models\Currency;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'currency_id' => Currency::first()->id ?? Currency::factory()->create([
                'name' => 'Philippine Peso',
                'code' => 'PHP',
                'symbol' => '₱'
            ]),
            'bill_due' => rand(1, 12),
            'bill_due_type' => Arr::random(config('company.due_date_types')),
            'invoice_due' => rand(1, 12),
            'invoice_due_type' => Arr::random(config('company.due_date_types')),
            'quote_due' => rand(1, 12),
            'quote_due_type' => Arr::random(config('company.due_date_types')),
            'invoice_prefix' => 'INV-',
            'invoice_next_number' => 1,
            'sales_order_prefix' => 'SO-',
            'sales_order_next_number' => 1,
            'purchase_order_prefix' => 'PO-',
            'purchase_order_next_number' => 1,
            'quote_prefix' => 'QU-',
            'quote_next_number' => 1,
            'credit_note_prefix' => 'CN-'
        ];
    }

    public function noCurrency ()
    {
        return $this->state(function (array $attributes) {
            return [
                'bill_due' => rand(1, 12),
                'bill_due_type' => Arr::random(config('company.due_date_types')),
                'invoice_due' => rand(1, 12),
                'invoice_due_type' => Arr::random(config('company.due_date_types')),
                'quote_due' => rand(1, 12),
                'quote_due_type' => Arr::random(config('company.due_date_types')),
                'invoice_prefix' => 'INV-',
                'invoice_next_number' => 1,
                'sales_order_prefix' => 'SO-',
                'sales_order_next_number' => 1,
                'purchase_order_prefix' => 'PO-',
                'purchase_order_next_number' => 1,
                'quote_prefix' => 'QU-',
                'quote_next_number' => 1,
                'credit_note_prefix' => 'CN-'
            ];
        });
    }
}
