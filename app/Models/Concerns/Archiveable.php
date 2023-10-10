<?php

namespace App\Models\Concerns;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

trait Archiveable
{
    /**
     * Boot up the Archivable trait
     * @return [type] [description]
     */
    public static function bootArchiveable()
    {
        /**
         * Tap into the eloquent booted event so we can add the extra observable events.
         * This will allow the use of observers for archiving events instead of static bindings.
         */
        app('events')->listen('eloquent.booted: ' . static::class, function ($model) {
            $model->addObservableEvents('archiving', 'archived', 'unarchiving', 'unarchived');
        });
    }
}
