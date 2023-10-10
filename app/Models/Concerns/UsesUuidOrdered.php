<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait UsesUuidOrdered
{
    protected static function bootUsesUuidOrdered()
    {
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::orderedUuid();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
