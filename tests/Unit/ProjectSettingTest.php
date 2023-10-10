<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminBaseTest;
use App\Models\ProjectSetting;
use App\Models\User;

class ProjectSettingTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testProjectSettingCreateIsHappy()
    {
        $user = User::factory()->create();

        $project_setting = ProjectSetting::factory()->make()->toArray();

        $response = $this->put("/api/users/$user->id/project-setting", $project_setting);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }
}
