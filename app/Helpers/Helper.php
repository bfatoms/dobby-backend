<?php

use Illuminate\Database\Eloquent\Relations\Relation;

if (!function_exists('getOrigin')) {
    function getOrigin()
    {
        return trim(request()->headers->get('origin'), "/") ?:
            trim(config('company.frontend_url'), "/");
    }
}


if (!function_exists('getMorphKey')) {
    function getMorphKey($key)
    {
        $relations = array_flip(Relation::morphMap());
        return optional($relations)[$key];
    }
}
