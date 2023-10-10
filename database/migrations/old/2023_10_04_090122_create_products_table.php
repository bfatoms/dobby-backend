<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
