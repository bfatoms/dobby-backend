<?php

namespace App\Models;

use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccountTransaction extends Model
{
    use Listable, Sortable, HasFactory;

    protected $table = "bank_account_transactions";

    protected $casts = [
        'id' => 'string',
        'spent' => 'float',
        'received' => 'float',
        'balance' => 'float',
        'transaction_date' => 'datetime'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}
