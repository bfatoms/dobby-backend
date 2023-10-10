<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWithQuantity extends Model
{
    use HasFactory;

    protected $table = "products_with_quantity";

    protected $casts = [
        'available_quantity' => 'integer'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
