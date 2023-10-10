<?php

namespace App\Models;

use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTransactionHistory extends Model
{
    use Sortable, Listable, HasFactory;
    
    protected $table = "product_transaction_history";

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
