<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\ChartOfAccountTypeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ImexController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\TransferMoneyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViewTableController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('status', fn() => response()->json(['message' => 'OK']));

Route::group(['middleware' => ['api']], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('forgot-password', [AuthController::class, 'forgot']);
        Route::get('verify/{verification_token}', [AuthController::class, 'verify']);
        Route::get('reset-password/{reset_token}/check', [AuthController::class, 'resetToken']);
        Route::put('reset-password/{reset_token}', [AuthController::class, 'reset']);
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('system/tax-rates', [SystemController::class, 'taxRates']);
        Route::get('system/accounts', [SystemController::class, 'accounts']);
        Route::get('system/tax-types', [SystemController::class, 'taxTypes']);
        Route::get('system/due-date-types', [SystemController::class, 'dueDateTypes']);
        Route::get('system/chart-of-account-types', [SystemController::class, 'chartOfAccountTypes']);
        Route::get('system/currencies', [SystemController::class, 'currencies']);
        Route::get('system/currencies-default', [SystemController::class, 'defaultCurrency']);
        Route::get('system/available-currencies', [SystemController::class, 'availableCurrencies']);
        Route::get('system/chart-of-accounts', [SystemController::class, 'chartOfAccounts']);
        Route::get('system/contacts', [SystemController::class, 'contacts']);
        Route::get('system/settings', [SystemController::class, 'settings']);
        Route::get('system/projects', [SystemController::class, 'projects']);
    });

    Route::group(['middleware' => 'auth:api'], function () {

        Route::post('{model}/import', [ImexController::class, 'import']);
        Route::get('{model}/export', [ImexController::class, 'export']);

        Route::get('permissions', [PermissionController::class, 'index']);


        Route::put('users/{user}/modules/{module}/permissions/{permission}/toggle', [PermissionController::class, 'toggle']);
        Route::delete('users/{user}/force-delete', [UserController::class, 'forceDelete']);
        Route::post('users/{user}/avatar', [UserController::class, 'avatar']);
        Route::delete('users/{user}/avatar', [UserController::class, 'trashAvatar']);
        Route::get('users/{user}/permissions', [UserController::class, 'permissions']);
        Route::put('users/{user}/project-setting', [UserController::class, 'updateProjectSetting']);
        Route::put('users/{user}/project-price', [UserController::class, 'updateProjectPrice']);
        Route::post('users/invite', [UserController::class, 'invite']);

        Route::get('users/{user}', [UserController::class, 'show']);
        Route::get('users', [UserController::class, 'index']);
        Route::post('users', [UserController::class, 'store']);
        Route::put('users/{user}', [UserController::class, 'update']);
        Route::delete('users/{user}', [UserController::class, 'destroy']);


        Route::get('tax-rates/{tax_rate}', [TaxRateController::class, 'show']);
        Route::get('tax-rates', [TaxRateController::class, 'index']);
        Route::post('tax-rates', [TaxRateController::class, 'store']);
        Route::put('tax-rates/{tax_rate}', [TaxRateController::class, 'update']);
        Route::delete('tax-rates/{tax_rate}', [TaxRateController::class, 'destroy']);


        Route::get('chart-of-account-types', [ChartOfAccountTypeController::class, 'index']);

        Route::get('chart-of-accounts/{chart_of_account}', [ChartOfAccountController::class, 'show']);
        Route::get('chart-of-accounts', [ChartOfAccountController::class, 'index']);
        Route::post('chart-of-accounts', [ChartOfAccountController::class, 'store']);
        Route::put('chart-of-accounts/{chart_of_account}', [ChartOfAccountController::class, 'update']);
        Route::delete('chart-of-accounts/{chart_of_account}', [ChartOfAccountController::class, 'destroy']);


        Route::get('banks/{id}/transactions', [ChartOfAccountController::class, 'transactions']);
        Route::get('banks/{id}/transaction-trends', [ChartOfAccountController::class, 'transactionTrends']);
        Route::get('banks/transaction-trends', [ChartOfAccountController::class, 'trends']);
        Route::delete('banks/{id}/transactions', [ChartOfAccountController::class, 'deleteTransactions']);

        Route::get('contacts/tax-types', [ContactController::class, 'taxTypes']);
        Route::get('contacts/due-date-types', [ContactController::class, 'dueDateTypes']);

        Route::get('contacts/{contact}', [ContactController::class, 'show']);
        Route::get('contacts', [ContactController::class, 'index']);
        Route::post('contacts', [ContactController::class, 'store']);
        Route::put('contacts/{contact}', [ContactController::class, 'update']);
        Route::delete('contacts/{contact}', [ContactController::class, 'destroy']);


        Route::get('products/search', [ProductController::class, 'search']);
        Route::get('products/{id}/transaction-history', [ProductController::class, 'transactionHistory']);
        Route::get('products/{product}', [ProductController::class, 'show']);
        Route::get('products', [ProductController::class, 'index']);
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{product}', [ProductController::class, 'update']);
        Route::delete('products/{product}', [ProductController::class, 'destroy']);


        Route::get('currencies/{currency}', [CurrencyController::class, 'show']);
        Route::get('currencies', [CurrencyController::class, 'index']);
        Route::post('currencies', [CurrencyController::class, 'store']);
        Route::put('currencies/{currency}', [CurrencyController::class, 'update']);
        Route::delete('currencies/{currency}', [CurrencyController::class, 'destroy']);


        Route::get('settings/{setting}', [SettingController::class, 'show']);
        Route::get('settings', [SettingController::class, 'index']);
        Route::post('settings', [SettingController::class, 'store']);
        Route::put('settings/{setting}', [SettingController::class, 'update']);
        Route::delete('settings/{setting}', [SettingController::class, 'destroy']);


        Route::get('order-activities', [OrderController::class, 'activities']);
        Route::get('orders/initial-data', [OrderController::class, 'initialData']);
        Route::post('orders/{order}/pay', [PaymentController::class, 'pay']);
        Route::post('orders/{order}/refund', [PaymentController::class, 'refund']);

        Route::get('orders/{order}', [OrderController::class, 'show']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::post('orders', [OrderController::class, 'store']);
        Route::put('orders/{order}', [OrderController::class, 'update']);
        Route::delete('orders/{order}', [OrderController::class, 'destroy']);


        Route::post('payments/credit-notes', [PaymentController::class, 'store']);
        Route::get('payments/{payment}', [PaymentController::class, 'show']);
        Route::get('payments', [PaymentController::class, 'index']);
        Route::post('payments', [PaymentController::class, 'store']);
        Route::put('payments/{payment}', [PaymentController::class, 'update']);
        Route::delete('payments/{payment}', [PaymentController::class, 'destroy']);


        Route::get('transfer-monies/{transfer_money}', [TransferMoneyController::class, 'show']);
        Route::get('transfer-monies', [TransferMoneyController::class, 'index']);
        Route::post('transfer-monies', [TransferMoneyController::class, 'store']);
        Route::put('transfer-monies/{transfer_money}', [TransferMoneyController::class, 'update']);
        Route::delete('transfer-monies/{transfer_money}', [TransferMoneyController::class, 'destroy']);

        Route::get('projects/search', [ProjectController::class, 'search']);
        Route::post('projects/{project}/quotation-fixed-amount', [ProjectController::class, 'createQuotationFixedAmount']);
        Route::post('projects/{project}/invoice-fixed-amount', [ProjectController::class, 'invoiceFixedAmount']);

        Route::post('projects/{project}/invoice', [ProjectController::class, 'createInvoice']);
        Route::post('projects/{project}/quotation', [ProjectController::class, 'createQuotation']);

        Route::get('projects/{project}/invoice-initial-data', [ProjectController::class, 'projectInvoiceInitialData']);
        Route::get('projects/{project}/quotation-initial-data', [ProjectController::class, 'projectQuotationInitialData']);

        Route::get('projects/{project}/quotations', [ProjectController::class, 'getProjectQuotations']);
        Route::get('projects/{project}/invoices', [ProjectController::class, 'getProjectInvoiceList']);

        Route::get('projects/{project}', [ProjectController::class, 'show']);
        Route::get('projects/{project}/price-settings', [ProjectController::class, 'getPriceSettings']);
        Route::get('projects', [ProjectController::class, 'index']);
        Route::post('projects', [ProjectController::class, 'store']);
        Route::put('projects/{project}', [ProjectController::class, 'update']);
        // Route::put('projects/{project}/price', [ProjectController::class, 'updatePrice']);
        Route::delete('projects/{project}', [ProjectController::class, 'destroy']);

        Route::get('tasks/{task}', [TaskController::class, 'show']);
        Route::get('tasks', [TaskController::class, 'index']);
        Route::post('tasks', [TaskController::class, 'store']);
        Route::put('tasks/{task}', [TaskController::class, 'update']);
        Route::delete('tasks/{task}', [TaskController::class, 'destroy']);

        Route::post('expenses/track', [ExpenseController::class, 'trackExpense']);
        Route::get('expenses/{expense}', [ExpenseController::class, 'show']);
        Route::get('expenses', [ExpenseController::class, 'index']);
        Route::post('expenses', [ExpenseController::class, 'store']);
        Route::put('expenses/{expense}', [ExpenseController::class, 'update']);
        Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy']);

        Route::get('time-entries/{time_entry}', [TimeEntryController::class, 'show']);
        Route::get('time-entries', [TimeEntryController::class, 'index']);
        Route::post('time-entries', [TimeEntryController::class, 'store']);
        Route::put('time-entries/{time_entry}', [TimeEntryController::class, 'update']);
        Route::delete('time-entries/{time_entry}', [TimeEntryController::class, 'destroy']);
    });

    Route::get('view-table', [ViewTableController::class, 'updateViewTable']);
});
