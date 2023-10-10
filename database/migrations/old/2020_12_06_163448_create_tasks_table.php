<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->string('name');
            $table->string('charge_type');
            $table->unsignedDecimal('rate', 19, 4);
            $table->string('status')->default('ON_GOING');
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->onDelete('cascade');
        });

        DB::statement("DROP VIEW IF EXISTS tasks_view");

        DB::statement("create view tasks_view as
            select tasks.*,
            '' as estimate_hours,
            '' as total_hours,
            '' as total_amount
            from tasks 
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
