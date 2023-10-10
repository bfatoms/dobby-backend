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
        Schema::create('transfer_monies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('from_bank_account_id');
            $table->unsignedBigInteger('to_bank_account_id');
            $table->decimal('from_amount', 19, 4);
            $table->decimal('to_amount', 19, 4);
            $table->text('reference')->nullable();
            $table->dateTime('transfer_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_monies');
    }
};
