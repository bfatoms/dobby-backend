<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid("project_id");
            $table->uuid("user_id");
            $table->unsignedDecimal('sales_price', 19, 4)->nullable();
            $table->unsignedDecimal('purchase_price', 19, 4)->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_prices');
    }
};
