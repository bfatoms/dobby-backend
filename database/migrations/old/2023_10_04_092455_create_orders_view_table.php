<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS orders_view");
    }
};
