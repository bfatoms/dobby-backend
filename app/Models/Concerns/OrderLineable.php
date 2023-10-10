<?php

namespace App\Models\Concerns;

use App\Models\OrderLine;

trait OrderLineable
{
    public function initializeOrderLineable()
    {
        // $this->appends[] = 'total';
    }

    public function orderLines()
    {
        return $this->hasMany(OrderLine::class, 'order_id');
    }

    public function getTotalAttribute()
    {
        $order_lines = $this->orderLines()->get();

        $sum = 0;
        foreach ($order_lines as $line) {
            $sum += round($line['quantity'] * $line['unit_price'] * (1 - $line['discount']) * (1 + $line['tax_rate']), 2);
        }

        return $sum;
    }
}
