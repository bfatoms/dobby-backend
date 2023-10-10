<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use App\Models\Currency;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BaseTest;
use App\Models\TaxRate;

class SystemTest extends BaseTest
{
    use RefreshDatabase;

    public function testSystemTaxRates()
    {
        $this->actingAs($this->adminWithPermissions());

        TaxRate::factory()->count(3)->create();

        $response = $this->get('/api/system/tax-rates');

        $response->assertStatus(200);

        $response->assertSee("name");

        $response->assertSee("rate");
    }

    public function testSystemAccounts()
    {
        $this->actingAs($this->adminWithPermissions());

        ChartOfAccount::factory()->create();

        $response = $this->get('/api/system/accounts');

        $response->assertStatus(200);

        $response->assertSee("name");

        $response->assertSee("code");
    }

    public function testSystemCurrencies()
    {
        $this->actingAs($this->adminWithPermissions());

        Currency::factory()->count(3)->create();

        $response = $this->get('/api/system/currencies');

        $response->assertStatus(200);

        $response->assertSee("name");

        $response->assertSee("symbol");

        $response->assertSee("code");
    }

    public function testSystemAvailableCurrencies()
    {
        $this->actingAs($this->adminWithPermissions());

        $response = $this->get('/api/system/available-currencies');

        $response->assertStatus(200);

        $response->assertSee("name");

        $response->assertSee("symbol");

        $response->assertSee("code");
    }

    public function testSystemChartOfAccountTypes()
    {
        $this->actingAs($this->adminWithPermissions());

        $response = $this->get('/api/system/chart-of-account-types');

        $response->assertStatus(200);

        $response->assertSee("current assets");

        $response->assertSee("overhead");

        $response->assertSee("non-current liabilities");
    }

    public function testSystemTaxTypes()
    {
        $this->actingAs($this->adminWithPermissions());

        $response = $this->get('/api/system/tax-types');

        $response->assertStatus(200);

        $response->assertSee("tax inclusive");

        $response->assertSee("tax exclusive");

        $response->assertSee("no tax");
    }

    public function testSystemDueDateTypes()
    {
        $this->actingAs($this->adminWithPermissions());

        $response = $this->get('/api/system/due-date-types');
        
        $response->assertStatus(200);

        $response->assertSee("of the following month");

        $response->assertSee("day(s) after order date");

        $response->assertSee("day(s) after the end of the order month");

        $response->assertSee("of the current month");
    }

    public function testSystemChartOfAccounts()
    {
        $this->actingAs($this->userWithoutPermissions());

        ChartOfAccount::factory()->withTaxRate()->create();

        $response = $this->get('/api/system/chart-of-accounts?with=taxRate&sortKey=name&sortOrder=asc');

        $response->assertStatus(200);

        $response->assertSee("code");

        $response->assertSee("name");

        $response->assertSee("type");

        $response->assertSee("last_page");
    }

    public function testdefaultCurrency()
    {
        $this->actingAs($this->userWithoutPermissions());

        Setting::factory()->create();

        $response = $this->get('/api/system/currencies-default');

        $response->assertStatus(200);
    }

    public function testSystemSettings()
    {
        $this->actingAs($this->userWithoutPermissions());

        $response = $this->get('/api/system/settings');

        $response->assertStatus(200);
    }

    public function testSystemContacts()
    {
        $this->actingAs($this->userWithoutPermissions());

        $response = $this->get('/api/system/contacts?sortKey=name&sortOrder=asc');

        $response->assertStatus(200);
    }

    public function testSystemContactsWithRelationships()
    {
        $this->actingAs($this->userWithoutPermissions());
        
        Project::factory()->create();

        $response = $this->get('/api/system/contacts?with=projects&sortKey=name&sortOrder=asc');

        $response->assertStatus(200);

        $response->assertSee('projects');
    }
}
