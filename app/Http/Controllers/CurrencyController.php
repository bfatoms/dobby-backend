<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Http\Requests\CurrencyCreateRequest;
use App\Http\Requests\CurrencyUpdateRequest;


class CurrencyController extends BaseController
{
    protected $model = Currency::class;

    protected $create_request = CurrencyCreateRequest::class;

    protected $update_request = CurrencyUpdateRequest::class;

    public function systemCurrencies()
    {
        $system_currencies = config('currencies');
        $currencies = [];
        foreach ($system_currencies as $currency) {
            $currencies[] = [
                'code' => $currency[0],
                'symbol' => $currency[1],
                'name' => $currency[2],
            ];
        }
        return $this->resolve($currencies, "SYSTEM_CURRENCIES");
    }
}
