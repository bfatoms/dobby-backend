<?php

namespace App\Models;

use App\Models\Concerns\Delible;
use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\UsesUuidOrdered;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    use Listable, UsesUuidOrdered, Sortable, Delible, HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'description',
        'type',
        'start_at',
        'end_at',
        'duration',
        'time_entry_date',
        'is_invoiced',
    ];

    protected $casts = [
        'duration' => 'float',
        'is_invoiced' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'time_entry_date' => 'datetime',
    ];

    public function task()
    {
        return $this->hasOne(Task::class, 'id', 'task_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function scopeMarkAsInvoiced($query)
    {
        return $query->update(['is_invoiced' => true]);
    }
}
