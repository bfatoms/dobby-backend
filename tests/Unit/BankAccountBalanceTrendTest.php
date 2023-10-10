<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;

class BankAccountBalanceTrendTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testBankAccountTrends()
    {
        // $this->withoutExceptionHandling();

        $bank = ChartOfAccount::factory()->create([
            'type' => 'bank',
            'currency_id' => $this->setting['currency_id']
        ]);

        Order::factory()->receiveMoneyDirect()->create([
            'bank_id' => $bank['id'],
            'total_amount' => 1000,
            'order_date' => '2020-02-01 16:00:00'
        ]);

        Order::factory()->spendMoneyDirect()->create([
            'bank_id' => $bank['id'],
            'total_amount' => 500,
            'order_date' => '2020-05-01 16:00:00'
        ]);

        $response = $this->json("GET", "/api/banks/" . $bank['id'] . "/transaction-trends?from=2020-01-01&to=2021-01-01");

        $response->assertJsonFragment(["transaction_date" => "2020-01-01"]);

        $response->assertJsonFragment([
            "transaction_date" => "2020-02-02",
            "balance" => 1000
        ]);

        $response->assertJsonFragment([
            "transaction_date" => "2020-05-02",
            "balance" => 500
        ]);
    }

    public function testBankAccountTrendsBatch()
    {
        // $this->withoutExceptionHandling();

        $bank = ChartOfAccount::factory()->create([
            'type' => 'bank',
            'currency_id' => $this->setting['currency_id']
        ]);

        Order::factory()->receiveMoneyDirect()->create([
            'bank_id' => $bank['id'],
            'total_amount' => 1000,
            'order_date' => '2020-02-01 16:00:00'
        ]);

        Order::factory()->spendMoneyDirect()->create([
            'bank_id' => $bank['id'],
            'total_amount' => 500,
            'order_date' => '2020-05-01 16:00:00'
        ]);
        
        $response = $this->json("GET", "/api/banks/transaction-trends?from=2020-01-01&to=2021-01-01");

        $response->assertJsonFragment(["transaction_date" => "2020-01-01"]);

        $response->assertJsonFragment([
            "transaction_date" => "2020-02-02",
            "balance" => 1000
        ]);

        $response->assertJsonFragment([
            "transaction_date" => "2020-05-02",
            "balance" => 500
        ]);
    }
}
