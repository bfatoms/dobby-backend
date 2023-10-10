<?php

namespace App\Models;

use App\Models\Concerns\Delible;
use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use Listable, Sortable, Delible, HasFactory;

    public static $currency_id = 1;

    protected $fillable = [
        'description',
        'name',
        'code',
        'tax_rate_id',
        'type',
        'is_system_account',
        'currency_id',
        'bank_name'
    ];

    protected $casts = [
        'is_system_account' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        self::$currency_id = optional(Setting::first())->currency_id ?: Setting::factory()->create()->currency_id;;

        static::deleting(function ($data) {
            if ($data['is_system_account'] === true) {
                abort(422, 'REMOVING_DEFAULT_ACCOUNT_IS_NOT_ALLOWED');
            }
        });
    }

    public function getCurrencyIdAttribute()
    {
        return $this->attributes['currency_id'] ?: self::$currency_id;
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function saleContacts()
    {
        return $this->hasMany(Contact::class, 'sale_account_id');
    }

    public function purchaseContacts()
    {
        return $this->hasMany(Contact::class, 'purchase_account_id');
    }

    public function purchaseProducts()
    {
        return $this->hasMany(Product::class, 'purchase_account_id');
    }

    public function saleProducts()
    {
        return $this->hasMany(Product::class, 'sale_account_id');
    }

    public function costOfGoodsSoldAccounts()
    {
        return $this->hasMany(Product::class, 'cost_of_goods_sold_account_id');
    }

    public function inventoryProducts()
    {
        return $this->hasMany(Product::class, 'inventory_asset_account_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
