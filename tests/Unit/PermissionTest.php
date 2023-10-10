<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BaseTest;

class PermissionTest extends BaseTest
{
    use RefreshDatabase;

    public function testPermissionList()
    {
        $this->actingAs($this->adminWithPermissions());

        $response = $this->get('/api/permissions');

        $response->assertStatus(200);

        $response->assertSee('module');

        $response->assertSee('action');

        $response->assertSee('create');
    }

    public function testUserWithPermissionToAssignPermissionCanAssignPermission()
    {
        $this->actingAs($this->adminWithPermissions());

        $user = User::factory()->create();

        $module = 'organization-settings';

        $action = 'assign-permission';

        $response = $this->put("/api/users/{$user['id']}/modules/$module/permissions/$action/toggle");

        $response->assertStatus(200);

        $response->assertSee('MODULE_PERMISSION_ENABLED');
    }

    public function testUserWithoutPermissionToAssignPermissionCannotAssignPermission()
    {
        $this->actingAs($this->userWithoutPermissions());

        $user = User::factory()->create();

        $module = 'organization-settings';

        $action = 'assign-permission';

        $response = $this->put("/api/users/{$user['id']}/modules/$module/permissions/$action/toggle");

        $response->assertStatus(403);
    }
}
