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
        DB::statement("DROP VIEW IF EXISTS products_with_quantity");

        DB::statement("create view products_with_quantity as
        select id as product_id, IF(is_tracked > 0, (select coalesce(sum(quantity), 0) from `order_lines` where exists (select * from `orders` where `order_lines`.`order_id` = `orders`.`id` and `status` in ('APPROVED', 'PAID') and `order_type` in ('BILL', 'BILL-CN', 'SMD') and `order_date` <= (select NOW())) and product_id = products.id) - (select coalesce(sum(quantity), 0) from `order_lines` where exists (select * from `orders` where `order_lines`.`order_id` = `orders`.`id` and `status` in ('APPROVED', 'PAID') and `order_type` in ('INV', 'INV-CN', 'RMD') and `order_date` <= (select NOW())) and product_id = products.id), null) as available_quantity from products");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS products_with_quantity");
    }
};
