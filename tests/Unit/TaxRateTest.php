<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;
use App\Models\TaxRate;

class TaxRateTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testTaxRateIndexIsHappy()
    {
        TaxRate::factory()->count(3)->create();

        $response = $this->get('/api/tax-rates');

        $response->assertStatus(200);

        $response->assertSee("name");

        $response->assertSee("rate");
    }

    public function testTaxRateCreateIsHappy()
    {
        $tax = TaxRate::factory()->make()->toArray();

        $tax['rate'] = $tax['rate']*100;

        $response = $this->post('/api/tax-rates', $tax);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testTaxRateCreateFieldRequired()
    {
        $tax = TaxRate::factory()->make()->toArray();

        $tax['rate'] = '';

        $tax['name'] = '';

        $response = $this->post('/api/tax-rates', $tax);

        $response->assertStatus(422);

        $response->assertSee("NAME_REQUIRED");

        $response->assertSee("RATE_REQUIRED");
    }

    public function testTaxRateCreateRateMustBeInteger()
    {
        $tax = TaxRate::factory()->make()->toArray();

        $response = $this->post('/api/tax-rates', $tax);

        $response->assertStatus(422);
        
        $response->assertSee("RATE_MUST_BE_POSITIVE_INTEGER");
    }

    public function testTaxRateCreateRateMax99()
    {
        $tax = TaxRate::factory()->make()->toArray();

        $tax['rate'] = 100;

        $response = $this->post('/api/tax-rates', $tax);

        $response->assertStatus(422);
        
        $response->assertSee("RATE_MAX_99");
    }

    public function testTaxRateShowIsHappy()
    {
        $tax = TaxRate::factory()->create();

        $response = $this->get("/api/tax-rates/{$tax['id']}?with=accounts,saleTaxRates,purchaseTaxRates");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_FOUND");
    }

    public function testTaxRateUpdateIsHappy()
    {
        $tax = TaxRate::factory()->create()->toArray();

        $tax['name'] = 'VAT 30%';

        $tax['rate'] = $tax['rate']*100;

        $response = $this->put("/api/tax-rates/{$tax['id']}", $tax);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }

    public function testTaxRateUpdateFieldsAreRequired()
    {
        $tax = TaxRate::factory()->create()->toArray();

        $tax['name'] = '';

        $tax['rate'] = '';

        $response = $this->put("/api/tax-rates/{$tax['id']}", $tax);

        $response->assertStatus(422);

        $response->assertSee("NAME_REQUIRED");

        $response->assertSee("RATE_REQUIRED");
    }

    public function testTaxRateUpdateRateMustBeIntegerCharacterInput()
    {
        $tax = TaxRate::factory()->create()->toArray();

        $tax['rate'] = "1asd";

        $response = $this->put("/api/tax-rates/{$tax['id']}", $tax);

        $response->assertStatus(422);

        $response->assertSee("RATE_MUST_BE_POSITIVE_INTEGER");
    }

    public function testTaxRateUpdateRateMustBeIntegerNegativeInput()
    {
        $tax = TaxRate::factory()->create()->toArray();

        $tax['rate'] = "-1";

        $response = $this->put("/api/tax-rates/{$tax['id']}", $tax);

        $response->assertStatus(422);

        $response->assertSee("RATE_MUST_BE_POSITIVE_INTEGER");
    }

    public function testTaxRateTrashIsHappy()
    {
        $tax = TaxRate::factory()->create();

        $response = $this->delete("/api/tax-rates/{$tax['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");
    }

    public function testTaxRateTrashSystemAccountIsNotAllowed()
    {        
        $tax = config('accounting.default_tax');

        $data = TaxRate::factory()->create($tax[0]);

        $response = $this->delete("/api/tax-rates/{$data['id']}");

        $response->assertStatus(422);

        $response->assertSee("REMOVING_DEFAULT_TAX_IS_NOT_ALLOWED");
    }
}
