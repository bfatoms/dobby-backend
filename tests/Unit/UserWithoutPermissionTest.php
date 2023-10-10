<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\UserBaseTest;

class UserWithoutPermissionTest extends UserBaseTest
{
    public function testUserWithoutPermissionCanIndex()
    {
        User::factory()->count(3)->create();

        $response = $this->get('/api/users');

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testUserWithoutPermissionCannotCreate()
    {
        $user = User::factory()->make()->toArray();

        $user['password'] = 'password';

        $response = $this->post('/api/users', $user);

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testUserWithoutPermissionCannotShow()
    {
        $user = User::factory()->create();

        $response = $this->get("/api/users/{$user['id']}");

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testUserWithoutPermissionCannotUpdate()
    {
        $user = User::factory()->create();

        $response = $this->put("/api/users/{$user['id']}", [
            'first_name' => 'charlene'
        ]);

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }

    public function testUserWithoutPermissionCannotTrash()
    {
        $user = User::factory()->create();

        $response = $this->delete("/api/users/{$user['id']}");

        $response->assertStatus(403);

        $response->assertSee("This action is unauthorized.");
    }
}
