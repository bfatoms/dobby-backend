<?php

namespace App\Models;

use App\Models\Concerns\Delible;
use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use Listable, Sortable, Delible, HasFactory;

    protected $fillable = [
        "rate",
        "name",
        "is_system_tax"
    ];

    protected $casts = [
        'is_system_tax' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($data) {
            if ($data['is_system_tax'] === true) {
                abort(422, 'REMOVING_DEFAULT_TAX_IS_NOT_ALLOWED');
            }
        });
    }

    public function setRateAttribute($rate)
    {
        $this->attributes['rate'] = $rate / 100;
    }

    public function saleTaxRates()
    {
        return $this->hasMany(Contact::class, 'sale_tax_rate_id');
    }

    public function purchaseTaxRates()
    {
        return $this->hasMany(Contact::class, 'purchase_tax_rate_id');
    }

    public function productSales()
    {
        return $this->hasMany(Product::class, 'sale_tax_rate_id');
    }

    public function productPurchases()
    {
        return $this->hasMany(Product::class, 'purchase_tax_rate_id');
    }

    public function orderLines()
    {
        return $this->hasMany(OrderLine::class, 'tax_rate_id');
    }

    public function chartOfAccounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function accounts()
    {
        return $this->hasMany(ChartOfAccount::class);
    }
}
