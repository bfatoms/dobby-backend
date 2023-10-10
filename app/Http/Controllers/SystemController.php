<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Contact;
use App\Models\Currency;
use App\Models\Project;
use App\Models\Setting;
use App\Models\TaxRate;


class SystemController extends BaseController
{

    public function taxRates()
    {
        return response()->json([
            'data' => TaxRate::get(['id', 'name', 'rate']),
            'message' => 'TAX_RATES'
        ], 200);
    }

    public function accounts()
    {
        return response()->json([
            'data' => ChartOfAccount::get(),
            'message' => 'ACCOUNTS'
        ], 200);
    }

    public function currencies()
    {
        return response()->json([
            'data' => Currency::get(['id', 'name', 'symbol', 'code']),
            'message' => 'CURRENCIES'
        ], 200);
    }

    public function availableCurrencies()
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

        return response()->json([
            'data' => $currencies,
            'message' => 'SYSTEM_CURRENCIES'
        ]);
    }

    public function defaultCurrency()
    {
        return $this->resolve(
            Currency::find(Setting::first()->currency_id),
            "DEFAULT_CURRENCY"
        );
    }

    public function chartOfAccounts()
    {
        $query = ChartOfAccount::when(request('with'), function ($q) {
            $q->with($this->parseWith());
        })
            ->when(request('sortKey'), function ($q) {
                $q->arrange(request('sortKey'), request('sortOrder'));
            });

        $query = $this->parseFields($query);

        return $this->resolve(
            $query->list(),
            "CHART_OF_ACCOUNTS"
        );
    }

    public function chartOfAccountTypes()
    {
        return response()->json([
            'data' => config('accounting.types'),
            'message' => 'ACCOUNT_TYPES'
        ], 200);
    }

    public function taxTypes()
    {
        return response()->json([
            'data' => config('company.tax_types'),
            'message' => 'TAX_TYPES'
        ], 200);
    }

    public function dueDateTypes()
    {
        return response()->json([
            'data' => config('company.due_date_types'),
            'message' => 'DUE_DATE_TYPES'
        ], 200);
    }

    public function contacts()
    {
        $query = Contact::when(request('sortKey'), function ($q) {
            $q->arrange(request('sortKey'), request('sortOrder'));
        })
        ->when(request('with'), function ($q) {
            $q->with(request('with'));
        });

        $query = $this->parseFields($query);

        return $this->resolve(
            $query->list(),
            "CONTACT_LIST"
        );
    }

    public function settings()
    {
        return $this->resolve(
            Setting::first(),
            "ORGANIZATION_SETTINGS"
        );
    }

    public function projects()
    {
        $query = Project::when(request('sortKey'), function ($q) {
            $q->arrange(request('sortKey'), request('sortOrder'));
        });

        $query = $this->parseFields($query);

        return $this->resolve(
            $query->list(),
            "PROJECT_LIST"
        );
    }
}
