<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\Attachable;
use App\Models\Concerns\Delible;
use App\Models\Concerns\OrderLineable;
use App\Models\Concerns\OrderPrefixNumberSetter;
use App\Models\Concerns\UsesUuidOrdered;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bill extends Model
{
    use Listable, Sortable, Attachable, UsesUuidOrdered, Delible, OrderLineable, OrderPrefixNumberSetter, HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_type',
        'contact_id',
        'order_date',
        'end_date',
        'order_number_prefix',
        'order_number',
        'reference',
        'tax_setting',
        'currency_id',
        'quotation_title',
        'quotation_summary',
        'status',
        'total_tax',
        'total_amount'
    ];

    protected $casts = [
        'order_number' => 'integer',
        'order_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function total()
    {
        $order_lines = $this->orderLines()->get();

        $sum = 0;
        
        foreach ($order_lines as $line) {
            $sum += round($line['qty'] * $line['unit_price'] * (1 - $line['discount']) * (1 + $line['tax_rate']), 2);
        }
        
        return $sum;
    }

    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'order_payments', 'order_id')->withPivot('amount');
    }

    public function refunds()
    {
        return $this->belongsToMany(Payment::class, 'order_payments', 'order_id')->withPivot('amount');
    }
}
