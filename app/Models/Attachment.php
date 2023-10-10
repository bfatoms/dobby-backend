<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'path',
        'name',
        'size',
        'extension',
        'type', // possible types are ['avatar', 'file'],
    ];

    protected $appends = [
        'url'
    ];

    public function getUrlAttribute()
    {
        $disk = null;
        if (config('app.env') == 'testing') {
            $disk = Storage::disk('local');
        } else {
            $disk = Storage::disk('wasabi');
        }
        return $disk->url($this->attributes['path']);
    }

    public function attachable()
    {
        return $this->morphTo();
    }
}
