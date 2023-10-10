<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidOrdered;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    use UsesUuidOrdered, HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'tax_rate',
        'tax_rate_id',
        'chart_of_account_id',
        'amount',
        'project_id',
        'expense_id'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'tax_rate' => 'float',
        'discount' => 'float',
        'order_id' => 'string',
        'product_id' => 'string',
        'project_id' => 'string',
        'expense_id' => 'string',
    ];

    public function getQuantityAttribute()
    {
        return abs($this->attributes['quantity']);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'order_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'order_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'order_id');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'order_id');
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class, 'order_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }

    public function expense()
    {
        return $this->hasOne(Expense::class, 'id', 'expense_id');
    }
}
