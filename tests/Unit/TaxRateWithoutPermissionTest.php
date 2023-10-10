<?php

namespace Tests\Unit;

use App\Models\TaxRate;
use Tests\UserBaseTest;

class TaxRateWithoutPermissionTest extends UserBaseTest
{
    public function testTaxRateWithoutPermissionCanIndex()
    {
        TaxRate::factory()->count(3)->create();

        $response = $this->get('/api/tax-rates');

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testTaxRateWithoutPermissionCannotCreate()
    {
        $tax = TaxRate::factory()->make()->toArray();

        $tax['password'] = 'password';

        $response = $this->post('/api/tax-rates', $tax);

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testTaxRateWithoutPermissionCannotShow()
    {
        $tax = TaxRate::factory()->create();

        $response = $this->get("/api/tax-rates/{$tax['id']}");

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testTaxRateWithoutPermissionCannotUpdate()
    {
        $tax = TaxRate::factory()->create();

        $response = $this->put("/api/tax-rates/{$tax['id']}", [
            'first_name' => 'charlene'
        ]);

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testTaxRateWithoutPermissionCannotTrash()
    {
        $tax = TaxRate::factory()->create();

        $response = $this->delete("/api/tax-rates/{$tax['id']}");

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }
}
