<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\User' => 'App\Policies\OrganizationPolicy',
        'App\Models\UserPermission' => 'App\Policies\OrganizationPolicy',
        'App\Models\TaxRate' => 'App\Policies\OrganizationPolicy',
        'App\Models\ChartOfAccount' => 'App\Policies\OrganizationPolicy',
        'App\Models\Currency' => 'App\Policies\OrganizationPolicy',
        'App\Models\Setting' => 'App\Policies\OrganizationPolicy',
        'App\Models\Contact' => 'App\Policies\ContactPolicy',
        'App\Models\ContacPerson' => 'App\Policies\ContactPolicy',
        'App\Models\Product' => 'App\Policies\ProductPolicy',

        'App\Models\Sale' => 'App\Policies\SaleInvoicePolicy',
        'App\Models\Invoice' => 'App\Policies\SaleInvoicePolicy',
        'App\Models\Purchase' => 'App\Policies\PurchaseBillPolicy',
        'App\Models\Bill' => 'App\Policies\PurchaseBillPolicy',
        'App\Models\Quote' => 'App\Policies\SaleInvoicePolicy',
        'App\Models\Order' => 'App\Policies\SaleInvoicePolicy',
        'App\Models\ReceiveMoney' => 'App\Policies\BankPolicy',
        'App\Models\SpendMoney' => 'App\Policies\BankPolicy',
        'App\Models\TransferMoney' => 'App\Policies\BankPolicy',
        'App\Models\BankAccountTransaction' => 'App\Policies\BankPolicy',

        'App\Models\Payment' => 'App\Policies\PaymentPolicy',
        'App\Models\Project' => 'App\Policies\ProjectPolicy',
        'App\Models\Task' => 'App\Policies\ProjectPolicy',
        'App\Models\Expense' => 'App\Policies\ProjectPolicy',
        'App\Models\TimeEntry' => 'App\Policies\ProjectPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
