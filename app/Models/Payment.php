<?php

namespace App\Models;

use App\Models\Concerns\Delible;
use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\UsesUuidOrdered;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use Sortable, Listable, Delible, UsesUuidOrdered, HasFactory;

    protected $increments = false;
    
    protected $fillable = [
        'paid_at',
        'chart_of_account_id',
        'reference',
        'exchange_rate',
        'order_id'
    ];

    protected $casts = [
        'exchange_rate' => 'float',
        'paid_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_payments')->withPivot(['amount', 'type'])->withTimestamps();
    }

    public function creditOrder()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
