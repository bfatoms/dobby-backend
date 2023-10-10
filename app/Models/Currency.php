<?php

namespace App\Models;

use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\Delible;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use Listable, Sortable, Delible, HasFactory;

    protected $fillable = [
        'name', 'code', 'symbol'
    ];

    public static function getWithDefault($currency_id = null)
    {
        $currencies = self::get();

        $default_currency_id = $currency_id ?: Setting::first()->currency_id;

        $formatted = [];

        foreach ($currencies as $currency) {
            $currency['is_default'] = false;
            if ($currency['id'] == $default_currency_id) {
                $currency['is_default'] = true;
            }
            $formatted[] = $currency;
        }
        return $formatted;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'currency_id');
    }

    public function chartOfAccounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'currency_id');
    }
}
