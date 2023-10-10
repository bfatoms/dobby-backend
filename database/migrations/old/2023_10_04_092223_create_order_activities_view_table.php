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
        DB::statement("DROP VIEW IF EXISTS order_activities");

        DB::statement("create view order_activities as select orders.id, 'order' as activity_type, status, IF(order_type in ('INV', 'INV-CN', 'RMP', 'QU', 'SO', 'PO'), concat(order_number_prefix, order_number), orders.reference) as order_number, order_type, reference as activity_reference, contact_id, order_date as date, updated_at, total_amount, if(order_type in ('INV', 'INV-CN', 'BILL', 'BILL-CN'), (select total_amount - ((select coalesce(sum(amount),0) from order_payments where order_id=orders.id) + (select coalesce(sum(amount), 0) from order_payments inner join payments on payments.id=order_payments.payment_id where payments.order_id=orders.id))), 0) as amount_due, currency_id from orders
            union
            select payments.id, 'payment' as activity_type, null as status, IF(order_type in ('INV'), concat(order_number_prefix,order_number), orders.reference) as order_number, order_type, payments.reference as activity_reference, contact_id, paid_at as date, payments.updated_at, (select coalesce(sum(amount), 0) from order_payments where order_payments.order_id=orders.id) as total_amount, null as amount_due, currency_id from orders inner join order_payments on order_payments.order_id=orders.id inner join payments on order_payments.payment_id=payments.id where order_type in ('INV', 'BILL')
            union
            select payments.id, 'refund' as activity_type, null as status, IF(order_type='INV', concat(order_number_prefix, order_number), orders.reference) as order_number, order_type, payments.reference as activity_reference, contact_id, paid_at as date, payments.updated_at, (select coalesce(sum(amount), 0) from order_payments where order_payments.order_id=orders.id) as total_amount, null as amount_due, currency_id from orders inner join order_payments on order_payments.order_id=orders.id inner join payments on order_payments.payment_id=payments.id where order_type in ('INV-CN', 'BILL-CN')
            union
            select payments.id, 'refund' as activity_type, null as status, IF(order_type='RMP', concat(order_number_prefix, order_number), orders.reference) as order_number, order_type, payments.reference as activity_reference, contact_id, paid_at as date, payments.updated_at, (select coalesce(sum(amount), 0) from order_payments where order_payments.order_id=orders.id) as total_amount, null as amount_due, currency_id from orders inner join order_payments on order_payments.order_id=orders.id inner join payments on order_payments.payment_id=payments.id where order_type in ('RMO', 'RMP', 'SMO', 'SMP');
            ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS order_activities");
    }
};
