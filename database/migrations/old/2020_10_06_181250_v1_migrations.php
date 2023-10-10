<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class V1Migrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
}
