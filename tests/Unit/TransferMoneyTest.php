<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BaseTest;
use App\Models\TransferMoney;
use App\Models\Setting;

class TransferMoneyTest extends BaseTest
{
    use RefreshDatabase;

    public function testTransferMoneyIndexIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        TransferMoney::factory()->count(3)->create();

        $response = $this->get('/api/transfer-monies');

        $response->assertStatus(200);
    }

    public function testTransferMoneyCreateIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $money = TransferMoney::factory()->make()->toArray();

        $response = $this->post('/api/transfer-monies', $money);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testTransferMoneyCreateFieldRequired()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $money = TransferMoney::factory()->make([
            'from_bank_account_id' => null,
            'to_bank_account_id' => null,
            'from_amount' => null,
            'to_amount' => null,
        ])->toArray();

        $response = $this->post('/api/transfer-monies', $money);

        $response->assertStatus(422);

        $response->assertSee("FROM_ACCOUNT_REQUIRED");

        $response->assertSee("TO_ACCOUNT_REQUIRED");

        $response->assertSee("FROM_AMOUNT_REQUIRED");

        $response->assertSee("TO_AMOUNT_REQUIRED");
    }

    public function testTransferMoneyShowIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $money = TransferMoney::factory()->create();

        $response = $this->get("/api/transfer-monies/{$money['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_FOUND");
    }

    public function testTransferMoneyUpdateIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $money = TransferMoney::factory()->create()->toArray();

        $money['to_amount'] = 500;

        $response = $this->put("/api/transfer-monies/{$money['id']}", $money);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }

    public function testTransferMoneyTrashIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $money = TransferMoney::factory()->create();

        $response = $this->delete("/api/transfer-monies/{$money['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");
    }
}
