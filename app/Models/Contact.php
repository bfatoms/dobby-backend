<?php

namespace App\Models;

use App\Models\Concerns\Delible;
use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\UsesUuidOrdered;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use Listable, UsesUuidOrdered, Sortable, Delible, HasFactory;

    protected $fillable = [
        'name',
        'mobile_number',
        'website',
        'address',
        'city',
        'zip',
        'sale_tax_type',
        'sale_account_id',
        'purchase_tax_type',
        'purchase_account_id',
        'tax_identification_number',
        'sale_tax_rate_id',
        'purchase_tax_rate_id',
        'business_registration_number',
        'credit_limit',
        'sale_discount',
        'bill_due',
        'bill_due_type',
        'invoice_due',
        'invoice_due_type',
    ];

    public function setSaleDiscountAttribute($discount)
    {
        $this->attributes['sale_discount'] = $discount / 100;
    }

    public function saleAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'sale_account_id');
    }

    public function purchaseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'purchase_account_id');
    }

    public function saleTaxRate()
    {
        return $this->belongsTo(TaxRate::class, 'sale_tax_rate_id');
    }

    public function purchaseTaxRate()
    {
        return $this->belongsTo(TaxRate::class, 'purchase_tax_rate_id');
    }

    public function contactPersons()
    {
        return $this->hasMany(ContactPerson::class, 'contact_id');
    }

    public function primaryPerson()
    {
        return $this->hasOne(ContactPerson::class, 'contact_id')
            ->where('is_primary', true);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class)->where('order_type', 'SO');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class)->where('order_type', 'PO');
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class)->where('order_type', 'QU');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class)->where('order_type', 'BILL');
    }

    public function billCreditNotes()
    {
        return $this->hasMany(Bill::class)->where('order_type', 'BILL-CN');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class)->where('order_type', 'INV');
    }

    public function invoiceCreditNotes()
    {
        return $this->hasMany(Invoice::class)->where('order_type', 'INV-CN');
    }

    public function spendMoney()
    {
        return $this->hasMany(Invoice::class)->whereIn('order_type', ['SMD', 'SMP', 'SMO']);
    }

    public function receiveMoney()
    {
        return $this->hasMany(Invoice::class)->whereIn('order_type', ['RMD', 'RMP', 'RMO']);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
