<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderView extends Model
{
    protected $table = 'orders_view';

    protected $casts = [
        'balance' => 'float',
        'amount_due' => 'float',
        'paid' => 'float'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
