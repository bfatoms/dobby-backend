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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_id');
            $table->uuid('payment_id');
            $table->decimal('amount', 19, 4)->default(0.0);
            $table->string('type')->default('single-payment');
            $table->string("payment_type", 10)->default("payment");
            $table->timestamps();
            $table->index(['payment_id', 'type']);
            $table->index(['type', 'payment_id']);

            $table->index(['order_id', 'type']);
            $table->index(['type', 'order_id']);
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
