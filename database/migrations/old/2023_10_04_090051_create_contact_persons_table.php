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
        Schema::create('contact_persons', function (Blueprint $table) {
            $table->id();
            $table->uuid('contact_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->boolean('include_in_emails')->default(false);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->foreign('contact_id')
                ->references('id')->on('contacts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_persons');
    }
};
