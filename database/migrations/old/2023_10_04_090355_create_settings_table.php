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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('currency_id')->nullable();

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

            $table->foreign('currency_id')
                ->references('id')->on('currencies')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
