<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;
use App\Models\ChartOfAccount;
use App\Models\TaxRate;

class ChartOfAccountTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testChartOfAccountIndexIsHappy()
    {
        TaxRate::factory()->count(3)->create();

        ChartOfAccount::factory()->count(3)->create();

        $response = $this->get('/api/chart-of-accounts');

        $response->assertStatus(200);

        $response->assertSee("name");

        $response->assertSee("type");
    }

    public function testChartOfAccountCreateIsHappy()
    {
        TaxRate::factory()->count(3)->create();

        $data = ChartOfAccount::factory()->withTaxRate()->make()->toArray();

        $response = $this->post('/api/chart-of-accounts', $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testChartOfAccountCreateFieldsAreRequired()
    {
        $data = ChartOfAccount::factory()->withTaxRate()->make()->toArray();

        $data['name'] = '';

        $data['code'] = '';

        $data['tax_rate_id'] = '';

        $data['type'] = '';

        $response = $this->post('/api/chart-of-accounts', $data);

        $response->assertStatus(422);

        $response->assertSee("NAME_REQUIRED");

        $response->assertSee("CODE_REQUIRED");

        $response->assertSee("TAX_RATE_REQUIRED");

        $response->assertSee("CODE_REQUIRED");
    }

    public function testChartOfAccountCreateCodeMax10Characters()
    {
        $data = ChartOfAccount::factory()->make()->toArray();

        $data['code'] = '12345678901';

        $response = $this->post('/api/chart-of-accounts', $data);

        $response->assertStatus(422);

        $response->assertSee("CODE_MAX_10_CHARACTERS");
    }

    public function testChartOfAccountCreateCodeExists()
    {
        $data = ChartOfAccount::factory()->withTaxRate()->create()->toArray();

        $data2 = ChartOfAccount::factory()->make(['code' => "{$data['code']}"])->toArray();

        $response = $this->post('/api/chart-of-accounts', $data2);

        $response->assertStatus(422);

        $response->assertSee("CODE_EXISTS");
    }

    public function testChartOfAccountCreateCodeAlphaNumericOnly()
    {
        $data = ChartOfAccount::factory()->make()->toArray();

        $data['code'] = 'Lou!3';

        $response = $this->post('/api/chart-of-accounts', $data);

        $response->assertStatus(422);

        $response->assertSee("CODE_ONLY_ALPHA_NUMERIC_ARE_ALLOWED");
    }

    public function testChartOfAccountCreateCodeMinimum3()
    {
        $data = ChartOfAccount::factory()->make()->toArray();

        $data['code'] = 'as';

        $response = $this->post('/api/chart-of-accounts', $data);

        $response->assertStatus(422);

        $response->assertSee("CODE_MIN_3_CHARACTERS");
    }

    public function testChartOfAccountShowIsHappy()
    {
        TaxRate::factory()->count(3)->create();

        $tax = ChartOfAccount::factory()->withTaxRate()->create();

        $response = $this->get("/api/chart-of-accounts/{$tax['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_FOUND");
    }

    public function testChartOfAccountUpdateIsHappy()
    {
        $data = ChartOfAccount::factory()->withTaxRate()->create()->toArray();

        $data['name'] = 'TESTUPDATE';

        $response = $this->put("/api/chart-of-accounts/{$data['id']}", $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");

        $response->assertSee("TESTUPDATE");
    }

    public function testChartOfAccountUpdateFieldsAreRequired()
    {
        $data = ChartOfAccount::factory()->withTaxRate()->create()->toArray();

        $data['name'] = '';

        $data['code'] = '';

        $data['type'] = '';

        $data['tax_rate_id'] = '';

        $response = $this->put("/api/chart-of-accounts/{$data['id']}", $data);

        $response->assertStatus(422);

        $response->assertSee("NAME_REQUIRED");

        $response->assertSee("CODE_REQUIRED");

        $response->assertSee("TAX_RATE_REQUIRED");

        $response->assertSee("CODE_REQUIRED");
    }

    public function testChartOfAccountUpdateAlphaNumericCodeOnly()
    {
        $data = ChartOfAccount::factory()->withTaxRate()->create()->toArray();

        $data['code'] = 'LOUI E';

        $response = $this->put("/api/chart-of-accounts/{$data['id']}", $data);

        $response->assertStatus(422);

        $response->assertSee("CODE_ONLY_ALPHA_NUMERIC_ARE_ALLOWED");
    }

    public function testChartOfAccountUpdateCodeMax10Characters()
    {
        $data = ChartOfAccount::factory()->withTaxRate()->create();

        $response = $this->put("/api/chart-of-accounts/{$data['id']}", [
            'code' => '12345678901'
        ]);

        $response->assertStatus(422);

        $response->assertSee("CODE_MAX_10_CHARACTERS");
    }

    public function testChartOfAccountTrashIsHappy()
    {
        $data = ChartOfAccount::factory()->withTaxRate()->create();
        
        $response = $this->delete("/api/chart-of-accounts/{$data['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");
    }

    public function testChartOfAccountMustAlwaysHaveDefaultCurrency()
    {        
        TaxRate::factory()->count(3)->create();

        ChartOfAccount::factory()->withTaxRate()->create();

        $response = $this->get('/api/chart-of-accounts');

        $response->assertStatus(200);

        $response->assertJsonFragment(['currency_id' => $this->setting['currency_id']]);
    }
}
