<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BaseTest;

class ChartOfAccountTypeTest extends BaseTest
{
    use RefreshDatabase;

    public function testChartOfAccountTypesList()
    {
        $this->actingAs($this->adminWithPermissions());

        $response = $this->get('/api/chart-of-account-types');

        $response->assertStatus(200);

        $response->assertSee('liabilities');

        $response->assertSee('other income');

        $response->assertSee('direct cost');
    }
}
