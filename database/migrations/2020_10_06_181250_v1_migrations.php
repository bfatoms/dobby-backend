<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('users', function ($table) {
            $table->uuid('verification_token')->nullable();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->dateTime('until');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('inactive');
            $table->string('role')->nullable();
            $table->string('department')->nullable();
            $table->dateTime('logged_in_at')->nullable();
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('attachable_type');
            $table->string('attachable_id');
            $table->string('path');
            $table->string('name');
            $table->string('size')->nullable();
            $table->string('extension', 10);
            $table->string('type', 20)->default('file'); // possible types are ['avatar', 'file', 'images']
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('module');
            $table->uuid('user_id');
            $table->string('action');
            $table->timestamps();
            $table->unique(['module', 'action', 'user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('birth_date')->nullable();
            $table->string('mobile_number', 30)->nullable();
            $table->string('website')->nullable();
            $table->string('language', 30)->nullable();
            $table->string('gender', 30)->nullable();
            $table->string('contact')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('github')->nullable();
            $table->string('codepen')->nullable();
            $table->string('slack')->nullable();
            $table->string('company')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('post_code', 20)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 50)->nullable();
        });

        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate', 5, 4);
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_rate_id')->nullable();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
            $table->foreign('tax_rate_id')
                ->references('id')->on('tax_rates')
                ->onDelete('restrict');
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->unsignedBigInteger('mobile_number')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip', 50)->nullable();
            $table->string('sale_tax_type')->nullable();
            $table->unsignedBigInteger('sale_account_id')->nullable();
            $table->string('purchase_tax_type')->nullable();
            $table->unsignedBigInteger('purchase_account_id')->nullable();
            $table->string('tax_identification_number')->nullable();
            $table->unsignedBigInteger('sale_tax_rate_id')->nullable();
            $table->unsignedBigInteger('purchase_tax_rate_id')->nullable();
            $table->string('business_registration_number')->nullable();
            $table->unsignedDecimal('credit_limit', 19, 4)->default(0.0000);
            $table->unsignedDecimal('sale_discount', 6, 4)->nullable();
            $table->unsignedInteger('bill_due')->nullable();
            // “Of the following month”,
            // “day(s) after bill date”,
            // “day(s) after the end of the bill month”,
            // “of the current month”
            $table->string('bill_due_type')->nullable();
            $table->unsignedInteger('invoice_due')->nullable();
            $table->string('invoice_due_type')->nullable();
            $table->timestamps();
            $table->foreign('sale_account_id')
                ->references('id')->on('chart_of_accounts')
                ->onDelete('set null');
            $table->foreign('purchase_account_id')
                ->references('id')->on('chart_of_accounts')
                ->onDelete('set null');
            $table->foreign('sale_tax_rate_id')
                ->references('id')->on('tax_rates')
                ->onDelete('set null');
            $table->foreign('purchase_tax_rate_id')
                ->references('id')->on('tax_rates')
                ->onDelete('set null');
        });

        Schema::create('contact_persons', function (Blueprint $table) {
            $table->id();
            $table->uuid('contact_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->boolean('include_in_emails')->default(false);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->foreign('contact_id')
                ->references('id')->on('contacts')
                ->onDelete('cascade');
        });

        Schema::table('tax_rates', function (Blueprint $table) {
            $table->unsignedDecimal('rate', 6, 4)->change();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->boolean('is_purchased')->default(false);
            $table->unsignedDecimal('purchase_price', 19, 4)->nullable();
            $table->unsignedBigInteger('purchase_tax_rate_id')->nullable();
            $table->unsignedBigInteger('purchase_account_id')->nullable();
            $table->text('purchase_description')->nullable();
            $table->unsignedBigInteger('cost_of_goods_sold_account_id')->nullable();

            $table->boolean('is_sold')->default(false);
            $table->unsignedDecimal('sale_price', 19, 4)->nullable();
            $table->unsignedBigInteger('sale_account_id')->nullable();
            $table->unsignedBigInteger('sale_tax_rate_id')->nullable();
            $table->text('sale_description')->nullable();

            $table->boolean('is_tracked')->default(false);
            $table->unsignedBigInteger('inventory_asset_account_id')->nullable();

            $table->timestamps();

            $table->foreign('purchase_account_id')
                ->references('id')->on('chart_of_accounts')
                ->onDelete('restrict');

            $table->foreign('cost_of_goods_sold_account_id')
                ->references('id')->on('chart_of_accounts')
                ->onDelete('restrict');

            $table->foreign('sale_account_id')
                ->references('id')->on('chart_of_accounts')
                ->onDelete('restrict');

            $table->foreign('inventory_asset_account_id')
                ->references('id')->on('chart_of_accounts')
                ->onDelete('restrict');

            $table->foreign('sale_tax_rate_id')
                ->references('id')->on('tax_rates')
                ->onDelete('restrict');

            $table->foreign('purchase_tax_rate_id')
                ->references('id')->on('tax_rates')
                ->onDelete('restrict');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('mobile_number')->nullable()->change();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('credit_limit')->nullable()->change();
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('symbol', 10);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('currency_id', 10);

            $table->unsignedInteger('bill_due');
            $table->string('bill_due_type');

            $table->unsignedInteger('invoice_due');
            $table->string('invoice_due_type');

            $table->unsignedInteger('quote_due');
            $table->string('quote_due_type');

            $table->string('invoice_prefix', 10)->default('INV-');
            $table->unsignedBigInteger('invoice_next_number')->default(1);

            $table->string('sales_order_prefix', 10)->default('SO-');
            $table->unsignedBigInteger('sales_order_next_number')->default(1);

            $table->string('purchase_order_prefix', 10)->default('PO-');
            $table->unsignedBigInteger('purchase_order_next_number')->default(1);

            $table->string('quote_prefix', 10)->default('QU-');
            $table->unsignedBigInteger('quote_next_number')->default(1);

            $table->string('credit_note_prefix', 10)->default('CN-');

            $table->timestamps();
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('currency_id');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedBigInteger('currency_id')->nullable();

            $table->foreign('currency_id')
                ->references('id')->on('currencies')
                ->onDelete('restrict');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string("order_type", 20);

            $table->uuid("contact_id");

            $table->dateTime('order_date');

            $table->dateTime('end_date');

            $table->string('order_number_prefix')->nullable();

            $table->unsignedBigInteger('order_number')->nullable();

            $table->longText('reference')->nullable();

            $table->string('tax_setting');

            $table->unsignedBigInteger('currency_id');

            $table->longText('quotation_title')->nullable();

            $table->longText('quotation_summary')->nullable();

            $table->string('status', 50)->default('DRAFT'); // APPROVED,DELETED etc.

            $table->timestamps();

            $table->foreign('contact_id')
                ->references('id')
                ->on('contacts')
                ->onDelete('restrict');

            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->onDelete('restrict');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('end_date')->nullable()->change();
        });

        Schema::create('order_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('order_id');

            $table->uuid('product_id')->nullable();

            $table->longText('description')->nullable();

            $table->decimal('quantity')->nullable();

            $table->unsignedDecimal('unit_price', 19, 4)->nullable();

            $table->unsignedDecimal('discount')->nullable();

            $table->unsignedDecimal('tax_rate')->nullable();

            $table->unsignedBigInteger('tax_rate_id')->nullable();

            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('restrict');

            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('cascade');

            $table->foreign('tax_rate_id')
                ->references('id')->on('tax_rates')
                ->onDelete('restrict');
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->boolean('is_system_account')->default(false);
            $table->longText('description')->nullable()->change();
        });

        Schema::table('tax_rates', function (Blueprint $table) {
            $table->boolean('is_system_tax')->default(false);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('exchange_rate', 40, 19)->default(1);
        });

        Schema::table('order_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('chart_of_account_id')->nullable();
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->string('bank_name')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedDecimal('total_amount', 19, 4)->nullable();
            $table->unsignedDecimal('total_tax', 19, 4)->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_prepayment')->default(false);
            $table->boolean('is_overpayment')->default(false);
        });

        Schema::table('order_lines', function (Blueprint $table) {
            $table->decimal('amount', 19, 4)->nullable();
        });

        Schema::create('transfer_monies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_bank_account_id');
            $table->unsignedBigInteger('to_bank_account_id');
            $table->decimal('from_amount', 19, 4);
            $table->decimal('to_amount', 19, 4);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->dateTime('paid_at');
            $table->unsignedBigInteger('chart_of_account_id')->nullable();
            $table->text('reference')->nullable();
            $table->decimal('exchange_rate', 19, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_id');
            $table->uuid('payment_id');
            $table->decimal('amount', 19, 4)->default(0.0);
            $table->timestamps();
            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->onDelete('cascade');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->uuid('order_id')->nullable();
        });

        Schema::table('transfer_monies', function (Blueprint $table) {
            $table->text('reference')->nullable();
            $table->dateTime('transfer_date')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_overpayment');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_prepayment');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('exchange_rate', 40, 19)->default(1)->change();
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->string('type')->default('single-payment');
        });

        Schema::table('transfer_monies', function (Blueprint $table) {
            $table->uuid('id')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')
                ->on('orders')->onDelete('restrict');
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->string("payment_type", 10)->default("payment");
        });

        Schema::table("orders", function (Blueprint $table) {
            $table->foreign('bank_id')->references('id')->on('chart_of_accounts')->onDelete('restrict');

            $table->index(['order_number_prefix', 'order_number']);
            $table->index(['order_number', 'order_number_prefix']);

            $table->index(['order_type', 'status']);
            $table->index(['status', 'order_type']);
        });

        Schema::table("order_payments", function (Blueprint $table) {
            $table->index(['payment_id', 'type']);
            $table->index(['type', 'payment_id']);

            $table->index(['order_id', 'type']);
            $table->index(['type', 'order_id']);
        });

        Schema::table('chart_of_accounts',  function (Blueprint $table) {
            $table->string('system_name', 20)->nullable();
        });

        DB::statement("DROP VIEW IF EXISTS bank_account_transactions");
        DB::statement("create view bank_account_transactions as select orders.id as id, 'order' as transaction_type, bank_id as account_id, order_date as transaction_date, contact_id, contacts.name, reference as description, null as payment_reference, 0 as spent, total_amount as received, orders.created_at, concat(order_date, ' ', orders.created_at) as transaction_unique_date from orders inner join contacts on contact_id=contacts.id where order_type in ('RMD', 'RMO', 'RMP')
            union
            select orders.id as id, 'order' as transaction_type, bank_id as account_id, order_date as transaction_date, contact_id, contacts.name, reference as description, null as payment_reference, total_amount as spent, 0 as received, orders.created_at, concat(order_date, ' ', orders.created_at) as transaction_unique_date from orders inner join contacts on contact_id=contacts.id where order_type in ('SMD', 'SMO', 'SMP')
            union
            select transfer_monies.id, 'transfer-money' as transaction_type, from_bank_account_id as account_id,  transfer_date as transaction_date, null as contact_id, null as name, reference as description, null as payment_reference, from_amount as spent, 0 as received, transfer_monies.created_at, concat(transfer_date, ' ', transfer_monies.created_at) as transaction_unique_date from transfer_monies
            union
            select transfer_monies.id, 'transfer-money' as transaction_type, to_bank_account_id as account_id, transfer_date as transaction_date, null as contact_id, null as name, reference as description, null as payment_reference, 0 as spent, to_amount as received, transfer_monies.created_at, concat(transfer_date, ' ', transfer_monies.created_at) as transaction_unique_date from transfer_monies
            union
            select payments.id, order_payments.type as transaction_type, chart_of_account_id as account_id, paid_at as transaction_date, contact_id, contacts.name, orders.reference as description, payments.reference as payment_reference, round(order_payments.amount * payments.exchange_rate, 5) as spent, 0 as received, payments.created_at, concat(paid_at, ' ', payments.created_at) as transaction_unique_date from payments inner join order_payments on payment_id=payments.id inner join orders on orders.id=order_payments.order_id inner join contacts on contacts.id=orders.contact_id where order_type in ('RMP', 'RMO', 'BILL') and order_payments.type='single-payment'
            union
            select payments.id, order_payments.type as transaction_type, chart_of_account_id as account_id, paid_at as transaction_date, contact_id, contacts.name, orders.reference as description, payments.reference as payment_reference, round(order_payments.amount * payments.exchange_rate, 5) as spent, 0 as received, payments.created_at, concat(paid_at, ' ', payments.created_at) as transaction_unique_date from payments inner join order_payments on payment_id=payments.id inner join orders on orders.id=order_payments.order_id inner join contacts on contacts.id=orders.contact_id where order_type in ('INV-CN') and order_payments.type='single-payment'
            union
            select payments.id, order_payments.type as transaction_type, chart_of_account_id as account_id, paid_at as transaction_date, contact_id, contacts.name, orders.reference as description, payments.reference as payment_reference, 0 as spent, round(order_payments.amount * payments.exchange_rate, 5) as received, payments.created_at, concat(paid_at, ' ', payments.created_at) as transaction_unique_date from payments inner join order_payments on payment_id=payments.id inner join orders on orders.id=order_payments.order_id inner join contacts on contacts.id=orders.contact_id where order_type in ('BILL-CN') and order_payments.type='single-payment'
            union
            select payments.id, order_payments.type as transaction_type, chart_of_account_id as account_id, paid_at as transaction_date, contact_id, contacts.name, orders.reference as description, payments.reference as payment_reference, 0 as spent, round(order_payments.amount * payments.exchange_rate, 5) as received, payments.created_at, concat(paid_at, ' ', payments.created_at) as transaction_unique_date from payments inner join order_payments on payment_id=payments.id inner join orders on orders.id=order_payments.order_id inner join contacts on contacts.id=orders.contact_id where order_type in ('SMP', 'SMO', 'INV') and order_payments.type='single-payment'
            union
            select payments.id, ANY_VALUE(order_payments.type) as transaction_type, chart_of_account_id as account_id, paid_at as transaction_date, ANY_VALUE(contact_id), ANY_VALUE(contacts.name), 'Batch Payment: Multiple items' as description, payments.reference as payment_reference, round(sum(order_payments.amount) * 1, 5) as spent, 0 as received, payments.created_at, concat(paid_at, ' ', payments.created_at) as transaction_unique_date from payments inner join order_payments on payment_id=payments.id inner join orders on orders.id=order_payments.order_id inner join contacts on contacts.id=orders.contact_id where order_type in ('RMP', 'RMO', 'BILL', 'BILL-CN') and order_payments.type='batch-payment' group by payments.id
            union
            select payments.id, ANY_VALUE(order_payments.type) as transaction_type, chart_of_account_id as account_id, paid_at as transaction_date, ANY_VALUE(contact_id), ANY_VALUE(contacts.name), 'Deposit: Multiple items' as description, payments.reference as payment_reference, 0 as spent, round(sum(order_payments.amount) * 1, 5) as received, payments.created_at, concat(paid_at, ' ', payments.created_at) as transaction_unique_date from payments inner join order_payments on payment_id=payments.id inner join orders on orders.id=order_payments.order_id inner join contacts on contacts.id=orders.contact_id where order_type in ('SMP', 'SMO', 'INV', 'INV-CN') and order_payments.type='batch-payment' group by payments.id;
            ");

        DB::statement("DROP VIEW IF EXISTS products_with_quantity");

        DB::statement("create view products_with_quantity as
        select id as product_id, IF(is_tracked > 0, (select coalesce(sum(quantity), 0) from `order_lines` where exists (select * from `orders` where `order_lines`.`order_id` = `orders`.`id` and `status` in ('APPROVED', 'PAID') and `order_type` in ('BILL', 'BILL-CN', 'SMD') and `order_date` <= (select NOW())) and product_id = products.id) - (select coalesce(sum(quantity), 0) from `order_lines` where exists (select * from `orders` where `order_lines`.`order_id` = `orders`.`id` and `status` in ('APPROVED', 'PAID') and `order_type` in ('INV', 'INV-CN', 'RMD') and `order_date` <= (select NOW())) and product_id = products.id), null) as available_quantity from products");

        DB::statement("DROP VIEW IF EXISTS order_activities");

        DB::statement("create view order_activities as select orders.id, 'order' as activity_type, status, IF(order_type in ('INV', 'INV-CN', 'RMP', 'QU', 'SO', 'PO'), concat(order_number_prefix, order_number), orders.reference) as order_number, order_type, reference as activity_reference, contact_id, order_date as date, updated_at, total_amount, if(order_type in ('INV', 'INV-CN', 'BILL', 'BILL-CN'), (select total_amount - ((select coalesce(sum(amount),0) from order_payments where order_id=orders.id) + (select coalesce(sum(amount), 0) from order_payments inner join payments on payments.id=order_payments.payment_id where payments.order_id=orders.id))), 0) as amount_due, currency_id from orders
            union
            select payments.id, 'payment' as activity_type, null as status, IF(order_type in ('INV'), concat(order_number_prefix,order_number), orders.reference) as order_number, order_type, payments.reference as activity_reference, contact_id, paid_at as date, payments.updated_at, (select coalesce(sum(amount), 0) from order_payments where order_payments.order_id=orders.id) as total_amount, null as amount_due, currency_id from orders inner join order_payments on order_payments.order_id=orders.id inner join payments on order_payments.payment_id=payments.id where order_type in ('INV', 'BILL')
            union
            select payments.id, 'refund' as activity_type, null as status, IF(order_type='INV', concat(order_number_prefix, order_number), orders.reference) as order_number, order_type, payments.reference as activity_reference, contact_id, paid_at as date, payments.updated_at, (select coalesce(sum(amount), 0) from order_payments where order_payments.order_id=orders.id) as total_amount, null as amount_due, currency_id from orders inner join order_payments on order_payments.order_id=orders.id inner join payments on order_payments.payment_id=payments.id where order_type in ('INV-CN', 'BILL-CN')
            union
            select payments.id, 'refund' as activity_type, null as status, IF(order_type='RMP', concat(order_number_prefix, order_number), orders.reference) as order_number, order_type, payments.reference as activity_reference, contact_id, paid_at as date, payments.updated_at, (select coalesce(sum(amount), 0) from order_payments where order_payments.order_id=orders.id) as total_amount, null as amount_due, currency_id from orders inner join order_payments on order_payments.order_id=orders.id inner join payments on order_payments.payment_id=payments.id where order_type in ('RMO', 'RMP', 'SMO', 'SMP');
            ");

        DB::statement("DROP VIEW IF EXISTS product_transaction_history");

        DB::statement("create view product_transaction_history as
            select products.id as product_id, products.name,
                orders.id as order_id,
                order_type,
                order_date,
                reference as order_reference,
                round(quantity) as quantity,
                (unit_price * 1-discount) as discounted_unit_price,
                currencies.code,
                (quantity * (unit_price * 1-discount)) as total,
                orders.created_at, orders.updated_at,
                concat(order_date, ' ', orders.created_at) as chronological_date
                from orders inner join order_lines on order_lines.order_id = orders.id inner join products on products.id=order_lines.product_id inner join currencies on currencies.id=orders.currency_id
                where product_id=products.id and orders.status in ('APPROVED', 'PAID') and order_type in ('BILL', 'INV', 'RMD', 'SMD')");

        DB::statement("DROP VIEW IF EXISTS orders_view");

        DB::statement("create view orders_view as select *, orders.id as order_id,
            concat(orders.order_number_prefix, orders.order_number) as full_order_number,
            (select coalesce(sum(amount), 0) from order_payments where order_id=orders.id) as paid,
            (select total_amount - ((select coalesce(sum(amount),0) from order_payments where order_id=orders.id) + (select coalesce(sum(amount),0) from order_payments inner join payments on payments.id=order_payments.payment_id where payments.order_id=orders.id)))  as amount_due,
            (select total_amount - ((select coalesce(sum(amount),0) from order_payments where order_id=orders.id) + (select coalesce(sum(amount),0) from order_payments inner join payments on payments.id=order_payments.payment_id where payments.order_id=orders.id))) as balance
            from orders;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
