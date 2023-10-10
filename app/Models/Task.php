<?php

namespace App\Models;

use App\Models\Concerns\Delible;
use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\UsesUuidOrdered;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use Listable, UsesUuidOrdered, Sortable, Delible, HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'charge_type',
        'rate',
        'status',
        'estimated_hours'
    ];

    protected $casts = [
        'rate' => 'float',
        'estimated_hours' => 'float'
    ];

    protected $hidden = [
        'estimate_hours',
        'total_hours',
        'total_amount'
    ];

    public function toArray()
    {
        $user = auth()->user();

        if ($user && $user->isAllowedTo('projects', 'all')) { // solves unit test issue
            $this->makeVisible($this->hidden);
        }

        return parent::toArray();
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class, 'task_id', 'id');
    }

    public function scopeMarkAsInvoiced($query)
    {
        return $query->update(['status' => 'INVOICED']);
    }
}
