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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS product_transaction_history");
    }
};
