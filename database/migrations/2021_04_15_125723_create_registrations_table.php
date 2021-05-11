<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('role_id')->constrained();
            $table->foreignId('company_id')->nullable()->constrained();
            $table->foreignId('employer_id')->nullable();
            $table->foreignId('course_class_id')->nullable()->constrained();
            $table->string('name');
            $table->date('birthdate')->nullable();
            $table->string('rg')->nullable();
            $table->string('cpf')->nullable();
            $table->string('ctps')->nullable();
            $table->string('responsable_name')->nullable();
            $table->string('responsable_cpf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['role_id']);
            $table->dropForeign(['company_id']);
            $table->dropForeign(['course_class_id']);
        });

        Schema::dropIfExists('registrations');
    }
}
