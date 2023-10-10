<?php

namespace App\Models;

use App\Models\Concerns\Delible;
use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\UsesUuidOrdered;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use Listable, UsesUuidOrdered, Sortable, Delible, HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'quantity',
        'unit_price',
        'charge_type',
        'mark_up',
        'custom_price',
        'is_invoiced',
        'is_estimated',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'mark_up' => 'float',
        'custom_price' => 'float',
        'is_invoiced' => 'boolean',
        'is_estimated' => 'boolean',
    ];

    public function project()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }

    public function trackedExpenses()
    {
        return $this->belongsToMany(Expense::class, 'tracked_expenses', 'estimated_expense_id', 'tracked_expense_id');
    }

    public function scopeMarkAsInvoiced($query)
    {
        return $query->update(['is_invoiced' => true]);
    }
}
