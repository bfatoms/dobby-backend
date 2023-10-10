<?php

namespace Tests\Unit;

use Tests\UserBaseTest;
use App\Models\TaxRate;
use App\Models\ChartOfAccount;

class ChartOfAccountWithoutPermissionTest extends UserBaseTest
{
    public function testChartOfAccountWithoutPermissionCanIndex()
    {
        TaxRate::factory()->count(3)->create();

        ChartOfAccount::factory()->count(3)->create();

        $response = $this->get('/api/chart-of-accounts');

        $response->assertStatus(403);
        
        $response->assertSee("This action is unauthorized.");
    }

    public function testChartOfAccountWithoutPermissionCannotCreate()
    {
        TaxRate::factory()->count(3)->create();

        $tax = ChartOfAccount::factory()->make()->toArray();

        $tax['password'] = 'password';

        $response = $this->post('/api/chart-of-accounts', $tax);

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testChartOfAccountWithoutPermissionCannotShow()
    {
        TaxRate::factory()->count(3)->create();

        $tax = ChartOfAccount::factory()->withTaxRate()->create();

        $response = $this->get("/api/chart-of-accounts/{$tax['id']}");

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testChartOfAccountWithoutPermissionCannotUpdate()
    {
        TaxRate::factory()->count(3)->create();

        $tax = ChartOfAccount::factory()->withTaxRate()->create();

        $response = $this->put("/api/chart-of-accounts/{$tax['id']}", [
            'first_name' => 'charlene'
        ]);

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testChartOfAccountWithoutPermissionCannotTrash()
    {
        TaxRate::factory()->count(3)->create();

        $tax = ChartOfAccount::factory()->withTaxRate()->create();

        $response = $this->delete("/api/chart-of-accounts/{$tax['id']}");

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }
}
