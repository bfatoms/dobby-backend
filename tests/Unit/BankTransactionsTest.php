<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use App\Models\Currency;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;
use App\Models\TransferMoney;

class BankTransactionsTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testBankCreateReceiveMoneyDirect()
    {
        $bank = ChartOfAccount::factory()->bankAccount()->create();

        $data = Order::factory()->receiveMoneyDirect()->create([
            'bank_id' => $bank->id,
            'total_amount' => 10000
        ]);
        
        $response = $this->json('GET', "api/banks/{$bank->id}/transactions?sortKey=transaction_date&sortOrder=desc");

        $response->assertStatus(200);

        $response->assertJsonFragment(['received' => $data['total_amount']]);

        $response->assertJsonFragment(['spent' => 0]);
        
        $response->assertJsonFragment(['balance' => $data['total_amount']]);
    }

    public function testBankCreateSpendMoneyDirect()
    {
        $bank = ChartOfAccount::factory()->bankAccount()->create();

        $data = Order::factory()->spendMoneyDirect()->create([
            'bank_id' => $bank->id,
            'total_amount' => 10000
        ]);

        $response = $this->json('GET', "api/banks/{$bank->id}/transactions?sortKey=transaction_date&sortOrder=desc");

        $response->assertStatus(200);

        $response->assertJsonFragment(['received' => 0]);

        $response->assertJsonFragment(['spent' => $data['total_amount']]);
        
        $response->assertJsonFragment(['balance' => ($data['total_amount'] * -1) ]);
    }

    public function testBankCreateReceiveAndSpendMoneyDirect()
    {
        $bank = ChartOfAccount::factory()->bankAccount()->create();

        $data = Order::factory()->spendMoneyDirect()->create([
            'bank_id' => $bank->id,
            'order_date' => now()->toIso8601String(),
            'total_amount' => 5000
        ]);
        
        $data2 = Order::factory()->receiveMoneyDirect()->create([
            'bank_id' => $bank->id,
            'order_date' => now()->addSeconds(2)->toIso8601String(),
            'total_amount' => 10000
        ]);

        $response = $this->json('GET', "api/banks/{$bank->id}/transactions?sortKey=transaction_date&sortOrder=desc");

        $response->assertStatus(200);

        $response->assertJsonFragment(['received' => 0]);

        $response->assertJsonFragment(['spent' => $data['total_amount']]);
        
        $response->assertJsonFragment(['balance' => ($data['total_amount'] * -1) ]);

        $response->assertJsonFragment(['received' => $data2['total_amount']]);

        $response->assertJsonFragment(['spent' => 0]);
        
        $response->assertJsonFragment(['balance' => ($data['total_amount'] - $data2['total_amount']) ]);
    }

    public function testBankCreateTransferMoneySameCurrency()
    {
        $bank1 = ChartOfAccount::factory()->bankAccount()->create();

        $bank2 = ChartOfAccount::factory()->bankAccount()->create();

        $transfer = TransferMoney::factory()->create([
            'from_bank_account_id' => $bank1->id,
            'to_bank_account_id' => $bank2->id,
            'from_amount' => 1000,
            'to_amount' => 1000
        ]);

        $response = $this->json('GET', "api/banks/{$bank1->id}/transactions");
        $response->assertJsonFragment(['spent' => $transfer['from_amount']]);

        $response = $this->json('GET', "api/banks/{$bank2->id}/transactions");
        $response->assertJsonFragment(['received' => $transfer['to_amount']]);
    }

    public function testBankCreateTransferMoneyDifferentCurrency()
    {
        $bank1 = ChartOfAccount::factory()->bankAccount()->create([
            'currency_id' => Currency::factory()->create()->id
        ]);

        $bank2 = ChartOfAccount::factory()->bankAccount()->create([
            'currency_id' => Currency::factory()->create()->id
        ]);

        $transfer = TransferMoney::factory()->create([
            'from_bank_account_id' => $bank1->id,
            'to_bank_account_id' => $bank2->id,
            'from_amount' => 1,
            'to_amount' => 50
        ]);

        $response = $this->json('GET', "api/banks/{$bank1->id}/transactions");

        $response->assertJsonFragment(['spent' => $transfer['from_amount']]);

        $response = $this->json('GET', "api/banks/{$bank2->id}/transactions");

        $response->assertJsonFragment(['received' => $transfer['to_amount']]);
    }
}
