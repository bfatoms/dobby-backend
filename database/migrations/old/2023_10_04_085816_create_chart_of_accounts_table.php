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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_rate_id')->nullable();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type')->nullable();
            $table->boolean('is_system_account')->default(false);
            $table->text('description')->nullable();
            $table->string('bank_name')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('system_name', 20)->nullable();

            $table->timestamps();
            $table->foreign('tax_rate_id')
                ->references('id')->on('tax_rates')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
