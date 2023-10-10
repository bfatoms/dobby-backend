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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->dateTime('paid_at');
            $table->unsignedBigInteger('chart_of_account_id')->nullable();
            $table->text('reference')->nullable();
            $table->decimal('exchange_rate', 40, 19)->default(1);
            $table->uuid('order_id')->nullable();
            $table->foreign('order_id')->references('id')
                ->on('orders')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
