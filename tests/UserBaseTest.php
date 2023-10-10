<?php

namespace Tests;

use App\Models\Setting;

abstract class UserBaseTest extends BaseTest
{
    public $setting = null;

    public function setUp() : void
    {
        parent::setUp();

        $this->actingAs($this->userWithoutPermissions());

        $this->setting = Setting::factory()->create();
    }
}
