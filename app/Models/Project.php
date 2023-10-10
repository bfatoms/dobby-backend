<?php

namespace App\Models;

use App\Models\Concerns\Delible;
use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\UsesUuidOrdered;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use Listable, UsesUuidOrdered, Sortable, Delible, HasFactory;

    protected $fillable = [
        'name',
        'contact_id',
        'deadline',
        'estimate',
        'status',
        'compute_estimate'
    ];

    protected $hidden = [
        'time_and_expenses',
        'is_invoiced',
        'cost',
        'profit'
    ];

    protected $casts = [
        'estimate' => 'float',
        'compute_estimate' => 'boolean',
        'deadline' => 'datetime'
    ];

    public function toArray()
    {
        $user = auth()->user();

        if ($user && $user->isAllowedTo('projects', 'all')) { // solves unit test issue
            $this->makeVisible($this->hidden);
        }

        return parent::toArray();
    }

    public function contact()
    {
        return $this->hasOne(Contact::class, 'id', 'contact_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id', 'id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'project_id', 'id');
    }

    public function price()
    {
        return $this->hasOne(ProjectPrice::class, 'id', 'project_id');
    }

    public function orderLines()
    {
        return $this->hasMany(OrderLine::class, 'project_id', 'id');
    }

    public function getDetails()
    {
        return DB::table('projects_view')->find($this->id);
    }
}
