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

class Order extends Model
{
    use Listable, Sortable, Attachable, UsesUuidOrdered, Delible, OrderPrefixNumberSetter, OrderLineable, HasFactory;

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
        'total_amount',
        'project_id',
        'expense_id',
    ];

    protected $casts = [
        'order_number' => 'integer',
        'total_amount' => 'float',
        'total_tax' => 'float',
        'amount_due' => 'float',
        'paid' => 'float',
        'balance' => 'float',
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

    public function payments()
    {
        // if bill, invoioce, its payments but it is still stored in order_payments_table
        return $this->belongsToMany(Payment::class, 'order_payments')->withPivot(['amount', 'type', 'payment_type'])->withTimestamps();
    }

    public function refunds()
    {
        return $this->belongsToMany(Payment::class, 'order_payments')->withPivot(['amount', 'type', 'payment_type'])->withTimestamps();
    }

    public function creditNotePayments()
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function ordersView()
    {
        return $this->hasOne(OrderView::class, 'order_id');
    }

    public function getAmountDue($payments = null)
    {
        return $this->ordersView()->first()['amount_due'];
    }

    public function getCreditNoteBalance($payments = null)
    {
        return $this->ordersView()->first()['balance'];
    }
}
