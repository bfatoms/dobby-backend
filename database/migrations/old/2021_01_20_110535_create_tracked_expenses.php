<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackedExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracked_expenses', function (Blueprint $table) {
            $table->id();
            $table->uuid('estimated_expense_id');
            $table->uuid('tracked_expense_id');

            $table->foreign('estimated_expense_id')
                ->references('id')->on('expenses')
                ->onDelete('cascade');

            $table->foreign('tracked_expense_id')
                ->references('id')->on('expenses')
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
        Schema::dropIfExists('tracked_expenses');
    }
}
