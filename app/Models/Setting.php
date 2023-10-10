<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_id',
        'bill_due',
        'bill_due_type',
        'invoice_due',
        'invoice_due_type',
        'quote_due',
        'quote_due_type',
        'invoice_prefix',
        'invoice_next_number',
        'sales_order_prefix',
        'sales_order_next_number',
        'purchase_order_prefix',
        'purchase_order_next_number',
        'quote_prefix',
        'quote_next_number',
        'credit_note_prefix'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public static function getNextNumber($type = 'SO')
    {
        $latest = Order::where('order_type', $type)->latest()->first();

        $settings = self::first();

        $types = config('company.ORDER_TYPES')[$type];

        if ($latest == null) {
            return $settings[$types['SETTING_NUMBER']];
        } elseif ( ($settings[$types['SETTING_NUMBER']] > ($latest['order_number']+1)) && !empty($types['SETTING_NUMBER']) ) {
            return $settings[$types['SETTING_NUMBER']];
        }

        return empty($types['SETTING_NUMBER']) ? null : ($latest['order_number']+1);
    }

    public static function getPrefix($order_type = 'SO')
    {
        $settings = self::first();
        
        $types = config('company.ORDER_TYPES')[$order_type];

        return $settings[$types['SETTING_PREFIX']];
    }

    public static function setNextNumber($order_number, $type)
    {
        $settings = self::first();

        $types = config('company.ORDER_TYPES')[$type];

        $settings[$types['SETTING_NUMBER']] = intval($order_number) + 1;

        $settings->save();
    }
}
