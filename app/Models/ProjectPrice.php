<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidOrdered;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPrice extends Model
{
    use UsesUuidOrdered, HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'sales_price',
        'purchase_price'
    ];

    protected $casts = [
        'sales_price' => 'float',
        'purchase_price' => 'float',
    ];
}
