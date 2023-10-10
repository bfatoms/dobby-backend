<?php

namespace Tests\Unit;

use App\Models\Currency;
use Tests\BaseTest;

class CurrencyTest extends BaseTest
{
    public function testCurrencyIndexIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        Currency::factory()->count(3)->create();

        $response = $this->get('/api/currencies');

        $response->assertStatus(200);
    }

    public function testCurrencyCreateIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Currency::factory()->make()->toArray();

        $response = $this->post('/api/currencies', $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testCurrencyCreateFieldsAreRequired()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = [
            'name' => '',
            'code' => '',
            'symbol' => '',
        ];

        $response = $this->post('/api/currencies', $data);

        $response->assertStatus(422);

        $response->assertSee("NAME_REQUIRED");

        $response->assertSee("CODE_REQUIRED");

        $response->assertSee("SYMBOL_REQUIRED");
    }

    public function testCurrencyCreateCodeIsUnique()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Currency::factory()->create()->toArray();

        $response = $this->post('/api/currencies', $data);

        $response->assertStatus(422);

        $response->assertSee("CODE_EXISTS");
    }

    public function testCurrencyCreateCodeMax3()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Currency::factory()->make()->toArray();

        $data['code'] = 'afzxczxc';

        $response = $this->post('/api/currencies', $data);

        $response->assertStatus(422);

        $response->assertSee("CODE_MUST_BE_A_MAX_3");
    }

    public function testCurrencyCreateCodeMin3()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Currency::factory()->make()->toArray();

        $data['code'] = 'af';

        $response = $this->post('/api/currencies', $data);

        $response->assertStatus(422);

        $response->assertSee("CODE_MUST_BE_A_MIN_3");
    }

    public function testCurrencyShowIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Currency::factory()->create()->toArray();

        $response = $this->get('/api/currencies/'.$data['id']);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_FOUND");
    }

    public function testCurrencyUpdateIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Currency::factory()->create()->toArray();

        $response = $this->put('/api/currencies/'.$data['id'], $data);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }

    public function testCurrencyUpdateFieldsAreRequired()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Currency::factory()->create()->toArray();

        $data['name'] = '';

        $data['code'] = '';

        $data['symbol'] = '';

        $response = $this->put('/api/currencies/'.$data['id'], $data);

        $response->assertStatus(422);

        $response->assertSee("NAME_REQUIRED");

        $response->assertSee("CODE_REQUIRED");

        $response->assertSee("SYMBOL_REQUIRED");
    }

    public function testCurrencyUpdateCodeIsUnique()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Currency::factory()->create()->toArray();

        $data2 = Currency::factory()->create()->toArray();

        $response = $this->put('/api/currencies/'.$data['id'], $data2);

        $response->assertStatus(422);

        $response->assertSee("CODE_EXISTS");
    }

    public function testCurrencyUpdateCodeMax3()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Currency::factory()->create()->toArray();

        $data['code'] = 'afzxczxc';

        $response = $this->put('/api/currencies/'.$data['id'], $data);

        $response->assertStatus(422);

        $response->assertSee("CODE_MUST_BE_A_MAX_3");
    }

    public function testCurrencyUpdateCodeMin3()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Currency::factory()->create()->toArray();

        $data['code'] = 'af';

        $response = $this->put('/api/currencies/'.$data['id'], $data);

        $response->assertStatus(422);

        $response->assertSee("CODE_MUST_BE_A_MIN_3");
    }

    public function testCurrencyTrashIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Currency::factory()->create()->toArray();

        $response = $this->delete('/api/currencies/'.$data['id']);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");
    }
}
