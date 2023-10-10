<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTimeEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('task_id');
            $table->uuid('user_id');
            $table->longText('description')->nullable();
            $table->string('type');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->unsignedDecimal('duration', 19, 4)->nullable();
            $table->dateTime('time_entry_date');
            $table->boolean('is_invoiced')->default(false);
            $table->timestamps();

            $table->foreign('task_id')
                ->references('id')->on('tasks')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

        DB::statement("DROP VIEW IF EXISTS time_entries_view");

        DB::statement("create view time_entries_view as
            select time_entries.*,
            tasks.name as task_name,
            users.first_name,
            users.last_name
            from time_entries 
            inner join users on users.id = time_entries.user_id
            inner join tasks on tasks.id = time_entries.task_id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_entries');
    }
}
