<?php

namespace App\Models;

use App\Models\Concerns\Attachable;
use App\Models\Concerns\Delible;
use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Listable, Sortable, Attachable, UsesUuid, Delible, HasFactory;

    protected $fillable = [
        'code',
        'name',
        'is_purchased',
        'purchase_price',
        'purchase_tax_rate_id',
        'purchase_account_id',
        'purchase_description',
        'cost_of_goods_sold_account_id',
        'is_sold',
        'sale_price',
        'sale_account_id',
        'sale_tax_rate_id',
        'sale_description',
        'is_tracked',
        'inventory_asset_account_id'
    ];

    protected $casts = [
        'is_tracked' => 'boolean',
        'is_purchased' => 'boolean',
        'is_sold' => 'boolean'
    ];

    protected $appends = [
        'available_quantity',
        'in_approved_purchase_orders',
        'in_approved_sales_orders',
        'in_approved_quotations'
    ];

    protected $with = [
        'quantityOnHand'
    ];

    public function setPurchasePriceAttribute($price)
    {
        $this->attributes['purchase_price'] = round($price, 4);
    }

    public function setSalePriceAttribute($price)
    {
        $this->attributes['sale_price'] = round($price, 4);
    }

    public function setPurchaseAccountIdAttribute($data)
    {
        $this->attributes['purchase_account_id'] = null;

        if ($this->attributes['is_purchased'] === true) {
            $this->attributes['purchase_account_id'] = $data;
        }
    }

    public function setCostOfGoodsSoldAccountId($data)
    {
        $this->attributes['cost_of_goods_sold_account_id'] = null;

        if ($this->attributes['is_tracked'] === true) {
            $this->attributes['cost_of_goods_sold_account_id'] = $data;
        }
    }

    public function setSaleAccountIdAttribute($data)
    {
        $this->attributes['sale_account_id'] = null;

        if ($this->attributes['is_sold'] === true) {
            $this->attributes['sale_account_id'] = $data;
        }
    }

    public function setInventoryAssetAccountId($data)
    {
        $this->attributes['inventory_asset_account_id'] = null;
        if ($this->attributes['is_tracked'] === true) {
            $this->attributes['inventory_asset_account_id'] = $data;
        }
    }

    public function getBillQuantity()
    {
        if ($this->is_tracked === true) {
            return OrderLine::whereHas('order', function ($query) {
                $query->whereIn('status', ['APPROVED', 'PAID'])
                    ->whereIn('order_type', ['BILL', 'SMD'])
                    ->where('order_date', "<=", now()->toDateTimeString());
            })->where('product_id', $this->id)->sum('quantity');
        }

        abort(422, 'TRYING_TO_GET_BILL_QUANTITY_THAT_IS_NON_TRACKED_ERROR');
    }

    public function getInvoiceQuantity()
    {
        if ($this->is_tracked === true) {
            return OrderLine::whereHas('order', function ($query) {
                $query->whereIn('status', ['APPROVED', 'PAID'])
                    ->whereIn('order_type', ['INV', 'RMD'])
                    ->where('order_date', "<=", now()->toDateTimeString());
            })->where('product_id', $this->id)->sum('quantity');
        }

        abort(422, 'TRYING_TO_GET_INVOICE_QUANTITY_THAT_IS_NON_TRACKED_ERROR');
    }

    public function getAvailableQuantity($order_line_id = null)
    {
        if ($this->is_tracked === true) {
            return intval($this->getBillQuantity($order_line_id)) - intval($this->getInvoiceQuantity($order_line_id));
        }

        abort(422, 'TRYING_TO_GET_AVAILABLE_QUANTITY_THAT_IS_NON_TRACKED_ERROR');
    }

    public function getAvailableQuantityAttribute()
    {
        return optional($this->quantityOnHand)['available_quantity'];
    }

    public function quantityOnHand()
    {
        return $this->hasOne(ProductWithQuantity::class, 'product_id');
    }

    public function purchaseTaxRate()
    {
        return $this->belongsTo(TaxRate::class, 'purchase_tax_rate_id');
    }

    public function saleTaxRate()
    {
        return $this->belongsTo(TaxRate::class, 'sale_tax_rate_id');
    }

    public function costOfGoodsSoldAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'cost_of_goods_sold_account_id');
    }

    public function purchaseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'purchase_account_id');
    }

    public function saleAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'sale_account_id');
    }

    public function inventoryAssetAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'inventory_asset_account_id');
    }

    public function getInApprovedPurchaseOrdersAttribute()
    {
        if ($this->is_purchased === true) {
            return intval(OrderLine::whereHas('order', function ($query) {
                $query->where('status', 'APPROVED')
                    ->where('order_type', 'PO');
            })->where('product_id', $this->id)->sum('quantity'));
        }

        return null;
    }

    public function getInApprovedSalesOrdersAttribute()
    {
        if ($this->is_sold === true) {
            return intval(OrderLine::whereHas('order', function ($query) {
                $query->where('status', 'APPROVED')
                    ->where('order_type', 'SO');
            })->where('product_id', $this->id)->sum('quantity'));
        }

        return null;
    }

    public function getInApprovedQuotationsAttribute()
    {
        if ($this->is_sold === true) {
            return intval(OrderLine::whereHas('order', function ($query) {
                $query->whereIn('status', ['SENT', 'ACCEPTED'])
                    ->where('order_type', 'QU');
            })->where('product_id', $this->id)->sum('quantity'));
        }

        return null;
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class, 'tax_rate_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function trackedAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'tracked_account_id');
    }
}
