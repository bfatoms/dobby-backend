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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid("contact_id");
            $table->dateTime('deadline')->nullable();
            $table->unsignedDecimal('estimate', 19, 4)->nullable();
            $table->string('status');
            $table->timestamps();

            $table->foreign('contact_id')
                ->references('id')->on('contacts')
                ->onDelete('cascade');
        });

        DB::statement("DROP VIEW IF EXISTS projects_view");

        DB::statement("create view projects_view as
            select projects.*,
            projects.id as project_id,
            contacts.name as contact_name,
            projects.name as project_name,
            '' as time_and_expenses,
            '' as is_invoiced,
            '' as cost,
            '' as profit
            from projects
            inner join contacts on contacts.id = projects.contact_id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
