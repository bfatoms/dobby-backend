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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string("order_type", 20);

            $table->uuid("contact_id");

            $table->dateTime('order_date');

            $table->dateTime('end_date')->nullable();

            $table->string('order_number_prefix')->nullable();

            $table->unsignedBigInteger('order_number')->nullable();

            $table->longText('reference')->nullable();

            $table->string('tax_setting');

            $table->unsignedBigInteger('currency_id');

            $table->longText('quotation_title')->nullable();

            $table->longText('quotation_summary')->nullable();

            $table->string('status', 50)->default('DRAFT'); // APPROVED,DELETED etc.

            $table->decimal('exchange_rate', 40, 19)->default(1);

            $table->unsignedDecimal('total_amount', 19, 4)->nullable();

            $table->unsignedDecimal('total_tax', 19, 4)->nullable();

            $table->unsignedBigInteger('bank_id')->nullable();

            $table->foreign('bank_id')->references('id')->on('chart_of_accounts')->onDelete('restrict');

            $table->index(['order_number_prefix', 'order_number']);
            
            $table->index(['order_number', 'order_number_prefix']);

            $table->index(['order_type', 'status']);
            
            $table->index(['status', 'order_type']);

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
