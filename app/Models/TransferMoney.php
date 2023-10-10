<?php

namespace App\Models;

use App\Models\Concerns\Listable;
use App\Models\Concerns\Delible;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\UsesUuidOrdered;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TransferMoney extends Model
{
    use Listable, Sortable, Delible, UsesUuidOrdered, HasFactory;

    protected $table = "transfer_monies";

    protected $increments = false;

    protected $fillable = [
        'from_bank_account_id',
        'to_bank_account_id',
        'from_amount',
        'to_amount',
        'reference',
        'transfer_date'
    ];

    protected $casts = [
        'to_amount' => 'float',
        'from_amount' => 'float',
        'transfer_date' => 'datetime',
    ];

    public function fromBankAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'from_bank_account_id');
    }

    public function toBankAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'to_bank_account_id');
    }
}
