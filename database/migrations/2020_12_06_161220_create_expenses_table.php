<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->string('name');
            $table->decimal('quantity');
            $table->unsignedDecimal('unit_price', 19, 4);
            $table->string('charge_type');
            $table->decimal('mark_up')->nullable();
            $table->unsignedDecimal('custom_price', 19, 4)->nullable();
            $table->boolean('is_invoiced')->default(false);
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->onDelete('cascade');
        });

        DB::statement("DROP VIEW IF EXISTS expenses_view");

        DB::statement("create view expenses_view as
            select expenses.*,
            quantity * unit_price * (1 + mark_up) as computed_mark_up,
            quantity * unit_price as pass_cost_along,
            custom_price * quantity as computed_custom_price,
            NULL as non_chargeable
            from expenses 
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
