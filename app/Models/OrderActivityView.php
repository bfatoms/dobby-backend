<?php

namespace App\Models;

use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;

class OrderActivityView extends Model
{
    use Listable, Sortable;

    protected $table = "order_activities";

    public $timestamps = false;

    protected $casts = [
        'total_amount' => 'float',
        'amount_due' => 'float',
        'id' => 'string',
        'date' => 'datetime',
        'updated_at' => 'datetime'

    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
