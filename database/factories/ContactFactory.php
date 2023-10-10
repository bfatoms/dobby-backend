<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use App\Models\ContactPerson;
use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'mobile_number' => (int) $this->faker->phoneNumber,
            'website' => "www.example.com",
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'zip' => $this->faker->postcode,
            'sale_tax_type' => Arr::random(config('company.tax_types')),
            'purchase_tax_type' => Arr::random(config('company.tax_types')),
            'tax_identification_number' => rand(10000, 99999),
            'business_registration_number' => rand(100000, 999999),
            'credit_limit' => rand(1000000, 9999999),
            'sale_discount' => rand(0.50, 0.01),
            'bill_due' => rand(1, 12),
            'bill_due_type' => Arr::random(config('company.due_date_types')),
            'invoice_due' => rand(1, 12),
            'invoice_due_type' => Arr::random(config('company.due_date_types')),
        ];
    }

    public function contactPerson()
    {
        return $this->state(function (array $attributes) {
            return [
                'contact_persons' => [
                    [
                        'first_name' => $this->faker->firstName,
                        'last_name' => $this->faker->lastName,
                        'email' => $this->faker->email,
                        'is_primary' => 1, // Arr::random([true, false]),
                        'include_in_emails' => 0, //Arr::random([true, false])
                    ]
                ]    
            ];
        });
    }

    public function withTaxAccountIds()
    {
        return $this->state(function (array $attributes) {
            return [
                'sale_account_id' => ChartOfAccount::factory()->create()->id,
                'purchase_account_id' => ChartOfAccount::factory()->create()->id,
                'sale_tax_rate_id' => TaxRate::factory()->create()->id,
                'purchase_tax_rate_id' => TaxRate::factory()->create()->id,
            ];
        });
    }
}
