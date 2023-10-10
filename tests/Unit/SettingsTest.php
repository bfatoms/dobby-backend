<?php

namespace Tests\Unit;

use App\Models\Setting;
use Tests\BaseTest;

class SettingsTest extends BaseTest
{
    public function testSettingIndexIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->count(2)->create();

        $response = $this->get('/api/settings');

        $response->assertStatus(200);
    }

    public function testSettingShowIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        Setting::factory()->create();

        $response = $this->get('/api/settings');

        $response->assertStatus(200);
    }

    public function testSettingCreateIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Setting::factory()->make()->toArray();

        $response = $this->post('/api/settings', $data);

        $response->assertStatus(422);

        $response->assertSee("CREATING_NEW_SETTING_NOT_ALLOWED_PLEASE_USE_UPDATE");
    }

    public function testSettingUpdateIsHappy()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Setting::factory()->create()->toArray();

        $response = $this->put('/api/settings/'.$data['id'], $data);

        $response->assertStatus(200);
    }

    public function testSettingTrashIsNotAllowed()
    {
        $this->actingAs($this->adminWithPermissions());

        $data = Setting::factory()->create()->toArray();

        $response = $this->delete('/api/settings/'.$data['id']);

        $response->assertStatus(422);
    }
}
