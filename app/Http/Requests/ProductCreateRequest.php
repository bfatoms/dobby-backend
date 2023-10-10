<?php

namespace App\Http\Requests;

use App\Rules\PurchaseAccountRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->defaults();
        // dd(request()->all());
        $purchase_account_rule = 'nullable';

        if ((request('is_tracked', false) === true) && (request('is_purchased', false) === true)) {
            $purchase_account_rule = 'nullable';
        } elseif (request('is_purchased', false) === true) {
            $purchase_account_rule = 'required';
        }

        return [
            'code' => 'required|unique:products,code',
            'name' => 'required',
            'is_purchased' => 'required_if:is_tracked,true|boolean',
            'purchase_price' => 'required_if:is_purchased,true|numeric|min:0|nullable',
            'purchase_tax_rate_id' => 'required_if:is_purchased,true',
            'purchase_account_id' => $purchase_account_rule,
            'purchase_description' => 'required_if:is_purchased,true',
            'cost_of_goods_sold_account_id' => 'required_if:is_tracked,true',
            'is_sold' => 'required_if:is_tracked,true|boolean',
            'sale_price' => 'required_if:is_sold,true|numeric|min:0|nullable',
            'sale_account_id' => 'required_if:is_sold,true',
            'sale_tax_rate_id' => 'required_if:is_sold,true',
            'sale_description' => 'required_if:is_sold,true',
            'is_tracked' => 'boolean',
            'inventory_asset_account_id' => 'required_if:is_tracked,true',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'CODE_REQUIRED',
            'code.unique' => 'CODE_EXISTS',
            'is_purchased.required_if' => 'IS_PURCHASED_REQUIRED',
            'is_purchased.boolean' => 'IS_PURCHASED_MUST_BE_BOOLEAN',
            'purchase_price.required_if' => 'PURCHASE_PRICE_REQUIRED',
            'purchase_price.min' => 'PURCHASE_PRICE_MIN_0',
            'purchase_account_id.required' => 'PURCHASE_ACCOUNT_REQUIRED',
            'purchase_tax_rate_id.required_if' => 'PURCHASE_TAX_RATE_REQUIRED',
            'purchase_description.required_if' => 'PURCHASE_DESCRIPTION_REQUIRED',
            'cost_of_goods_sold_account_id.required_if' => 'COST_OF_GOODS_SOLD_ACCOUNT_REQUIRED',
            'is_sold.required_if' => 'IS_SOLD_REQUIRED',
            'is_sold.boolean' => 'IS_SOLD_MUST_BE_BOOLEAN',
            'sale_price.required_if' => 'SALE_PRICE_REQUIRED',
            'sale_price.min' => 'SALE_PRICE_MIN_0',
            'sale_account_id.required_if' => 'SALE_ACCOUNT_REQUIRED',
            'sale_tax_rate_id.required_if' => 'SALE_TAX_RATE_REQUIRED',
            'sale_description.required_if' => 'SALE_DESCRIPTION_REQUIRED',
            'is_tracked.boolean' => 'IS_TRACKED_MUST_BE_BOOLEAN',
            'inventory_asset_account_id.required_if' => 'INVENTORY_ASSET_ACCOUNT_IS_REQUIRED'
        ];
    }

    public function defaults()
    {
        $defaults = [
            'is_tracked' => false,
            'is_purchased' => false,
            'is_sold' => false,
        ];

        foreach ($defaults as $key => $default) {
            if (request($key) === null) {
                request()->merge([$key => $default]);
            }
        }
    }
}
