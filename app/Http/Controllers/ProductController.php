<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductSearchRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Models\ProductTransactionHistory;
use Exception;
use Illuminate\Support\Facades\DB;


class ProductController extends BaseController
{
    protected $model = Product::class;

    protected $create_request = ProductCreateRequest::class;

    protected $update_request = ProductUpdateRequest::class;

    public function search(ProductSearchRequest $request)
    {
        $query = $this->model::with(['account', 'taxRate'])
            ->where(function ($query) {
                $search = request('search');
                $query->where('code', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%");
            });

        if (in_array($request->order_type, ['QU', 'SO', 'INV', 'INV-CN', 'RMD'])) {
            $query->where('is_sold', true);

            $fields = [
                'id',
                'code',
                'name',
                'is_tracked',
                'is_sold',
                'is_purchased',
                'sale_price as price',
                'sale_account_id as account_id',
                'sale_tax_rate_id as tax_rate_id',
                'sale_description as description',
            ];

            return $this->resolve($query->list(25, $fields));
        } elseif (in_array($request->order_type, ['PO', 'BILL', 'BILL-CN', 'SMD'])) {
            $query->where('is_purchased', true);

            $fields = [
                'id',
                'code',
                'name',
                'is_tracked',
                'is_sold',
                'is_purchased',
                'purchase_price as price',
                'purchase_tax_rate_id as tax_rate_id',
                'purchase_description as description',
                DB::raw('(CASE 
                WHEN is_tracked = 1 THEN inventory_asset_account_id
                ELSE purchase_account_id
                END) AS account_id')
            ];

            $query->select($fields);

            //dd($query->toSql());

            return $this->resolve($query->list(25));
        } else {
            $query->when(request('is_purchased'), function ($q) {
                $q->where('is_purchased', request('is_purchased'));
            });

            $query->when(request('is_sold'), function ($q) {
                $q->where('is_sold', request('is_sold'));
            });

            $query->select([
                'id',
                'code',
                'name',
                'sale_price',
                'purchase_price',
                'is_sold',
                'is_purchased'
            ]);

            $result = $query->list(25);

            $message = "RESOURCE_LIST";
            
            if (empty($result->data->data)) {
                $message = "NO_PRODUCTS_FOUND";
            }

            return $this->resolve($result, $message);
        }
        //dd('lasdas');
        return $this->resolve([], 'NO_PRODUCTS_FOUND');
    }

    public function transactionHistory($id)
    {
        $query = ProductTransactionHistory::where('product_id', $id)->orderBy('chronological_date', 'desc')
            ->when(request('sortKey') == 'created_at', function ($query) {
                $query->orderBy('chronological_date', request('sortOrder'));
            });

        return $this->resolve($query->list(), "TRANSACTION_HISTORY");
    }
}
