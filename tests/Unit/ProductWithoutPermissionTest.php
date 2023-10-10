<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\UserBaseTest;
use App\Models\Product;

class ProductWithoutPermissionTest extends UserBaseTest
{
    use RefreshDatabase;

    public function testProductIndexTrackedNonTrackedSoldAndPurchasedCanWithoutPermissions()
    {
        Product::factory()->create();

        Product::factory()->tracked()->create();

        Product::factory()->purchased()->create();

        Product::factory()->sold()->create();

        $response = $this->get('/api/products');

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testProductCreateTrackedCannotWithoutPermissions()
    {
        $data = Product::factory()->tracked()->make()->toArray();

        $response = $this->post('/api/products', $data);

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testProductUpdateNonTrackedCannotWithoutPermissions()
    {
        $data = Product::factory()->create()->toArray();

        $data['name'] = 'TEST_UPDATE';

        $response = $this->put('/api/products/' . $data['id'], $data);

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testProductTrashCannotWithoutPermissions()
    {
        $data = Product::factory()->create();

        $response = $this->delete('/api/products/' . $data['id']);

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }
}
