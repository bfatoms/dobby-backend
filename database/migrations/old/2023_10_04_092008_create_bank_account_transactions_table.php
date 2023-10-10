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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS bank_account_transactions");
    }
};
