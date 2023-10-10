<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Currency;
use App\Models\OrderLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'contact_id' => Contact::factory()->withTaxAccountIds()->create()->id,
            'order_date' => now()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'reference' => $this->faker->paragraph,
            'tax_setting' => array_rand(config('company.tax_types')),
            'currency_id' => Currency::factory()->create()->id,
            'quotation_title' => '',
            'quotation_summary' => '',
            'status' => 'APPROVED',
            'exchange_rate' => 1,
            'total_amount' => 0.0,
            'total_tax' => 0.0
        ];
    }

    public function name()
    {
        return $this->state(function (array $attributes) {
            return [];
        });
    }

    public function bill()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'BILL',
            ];
        });
    }

    public function billCn()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'BILL-CN',
            ];
        });
    }

    public function invoice()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'INV'
            ];
        });
    }

    public function invoiceCn()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'INV-CN'
            ];
        });
    }

    public function purchase()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'PO'
            ];
        });
    }

    public function sales()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'SO'
            ];
        });
    }

    public function quote()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'QU'
            ];
        });
    }

    public function receiveMoneyDirect()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'RMD'
            ];
        });
    }

    public function spendMoneyDirect()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'SMD'
            ];
        });
    }

    public function receiveMoneyPrepayment()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'RMP'
            ];
        });
    }

    public function spendMoneyPrepayment()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'SMP'
            ];
        });
    }

    public function receiveMoneyOverpayment()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'RMO'
            ];
        });
    }

    public function spendMoneyOverpayment()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_type' => 'SMO'
            ];
        });
    }

    public function orderLines()
    {
        return $this->state(function (array $attributes) {
            $order_lines = [
                OrderLine::factory()->make()->toArray(),
                OrderLine::factory()->make()->toArray(),
                OrderLine::factory()->make()->toArray(),
            ];

            return [
                'order_lines' => $order_lines,
                'total_amount' => collect($order_lines)->sum('amount'),
            ];
        });
    }

    public function orderLinesEmptyProduct()
    {
        return $this->state(function (array $attributes) {
            $order_lines = [
                OrderLine::factory()->make(['product_id' => ""])->toArray(),
                OrderLine::factory()->make(['product_id' => ""])->toArray(),
                OrderLine::factory()->make(['product_id' => ""])->toArray(),
            ];

            return [
                'order_lines' => $order_lines,
                'total_amount' => collect($order_lines)->sum('amount'),
            ];
        });
    }

    public function orderLinesEmptyProductReceivable()
    {
        return $this->state(function (array $attributes) {
            $order_lines = [
                OrderLine::factory()->make(['product_id' => "", "chart_of_account_id" => "1"])->toArray(),
                OrderLine::factory()->make(['product_id' => "", "chart_of_account_id" => "1"])->toArray(),
                OrderLine::factory()->make(['product_id' => "", "chart_of_account_id" => "1"])->toArray(),
            ];

            return [
                'order_lines' => $order_lines,
                'total_amount' => collect($order_lines)->sum('amount'),
            ];
        });
    }

    public function orderLinesEmptyProductPayable()
    {
        return $this->state(function (array $attributes) {
            $order_lines = [
                OrderLine::factory()->make(['product_id' => "", "chart_of_account_id" => "2"])->toArray(),
                OrderLine::factory()->make(['product_id' => "", "chart_of_account_id" => "2"])->toArray(),
                OrderLine::factory()->make(['product_id' => "", "chart_of_account_id" => "2"])->toArray(),
            ];

            return [
                'order_lines' => $order_lines,
                'total_amount' => collect($order_lines)->sum('amount'),
            ];
        });
    }


    public function orderLinesWithProject()
    {
        return $this->state(function (array $attributes) {
            $order_lines = [
                OrderLine::factory()->project()->make()->toArray(),
                OrderLine::factory()->make()->toArray(),
            ];

            return [
                'order_lines' => $order_lines,
                'total_amount' => collect($order_lines)->sum('amount'),
            ];
        });
    }

    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'DRAFT'
            ];
        });
    }

    public function forApproval()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'FOR_APPROVAL'
            ];
        });
    }
    
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'APPROVED'
            ];
        });
    }
    
    public function invoiced()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'INVOICED'
            ];
        });
    }

    
    public function billed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'BILLED'
            ];
        });
    }

        
    public function paid()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'PAID'
            ];
        });
    }

        
    public function deleted()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'DELETED'
            ];
        });
    }

        
    public function voided()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'VOID'
            ];
        });
    }

        
    public function sent()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'SENT'
            ];
        });
    }

        
    public function accepted()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'ACCEPTED'
            ];
        });
    }
}
