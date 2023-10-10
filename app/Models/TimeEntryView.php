<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeEntryView extends Model
{
    protected $table = 'time_entries_view';

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
}
