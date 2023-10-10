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

        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('mobile_number')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip', 50)->nullable();
            $table->string('sale_tax_type')->nullable();
            $table->unsignedBigInteger('sale_account_id')->nullable();
            $table->string('purchase_tax_type')->nullable();
            $table->unsignedBigInteger('purchase_account_id')->nullable();
            $table->string('tax_identification_number')->nullable();
            $table->unsignedBigInteger('sale_tax_rate_id')->nullable();
            $table->unsignedBigInteger('purchase_tax_rate_id')->nullable();
            $table->string('business_registration_number')->nullable();
            $table->unsignedDecimal('credit_limit', 19, 4)->nullable()->default(0.0000);
            $table->unsignedDecimal('sale_discount', 6, 4)->nullable();
            $table->unsignedInteger('bill_due')->nullable();
            // “Of the following month”,
            // “day(s) after bill date”,
            // “day(s) after the end of the bill month”,
            // “of the current month”
            $table->string('bill_due_type')->nullable();
            $table->unsignedInteger('invoice_due')->nullable();
            $table->string('invoice_due_type')->nullable();

            $table->timestamps();
            $table->foreign('sale_account_id')
                ->references('id')->on('chart_of_accounts')
                ->onDelete('set null');
            $table->foreign('purchase_account_id')
                ->references('id')->on('chart_of_accounts')
                ->onDelete('set null');
            $table->foreign('sale_tax_rate_id')
                ->references('id')->on('tax_rates')
                ->onDelete('set null');
            $table->foreign('purchase_tax_rate_id')
                ->references('id')->on('tax_rates')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
