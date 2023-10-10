<?php

namespace Tests;

use App\Models\Setting;

abstract class AdminBaseTest extends BaseTest
{
    public $setting = null;

    public function setUp() : void
    {
        parent::setUp();

        $this->actingAs($this->adminWithPermissions());

        $this->seedDatabase();

        $this->setting = Setting::factory()->create();
    }
}
