<?php

namespace App\Models\Concerns;

use App\Models\Setting;

trait Settable
{
    public static function bootSettable()
    {
        self::$currency_id = Setting::first()->currency_id;
    }
}
