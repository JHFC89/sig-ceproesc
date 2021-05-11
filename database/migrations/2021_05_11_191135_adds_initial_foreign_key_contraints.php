<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsInitialForeignKeyContraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('course_class_id')
                  ->references('id')
                  ->on('course_classes');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->foreign('discipline_id')
                  ->references('id')
                  ->on('disciplines');
        });

        Schema::table('course_classes', function (Blueprint $table) {
            $table->foreign('course_id')
                  ->references('id')
                  ->on('courses');
        });

        Schema::table('phones', function (Blueprint $table) {
            $table->foreign('registration_id')
                  ->references('id')
                  ->on('registrations');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->foreign('registration_id')
                  ->references('id')
                  ->on('registrations');
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->foreign('registration_id')
                  ->references('id')
                  ->on('registrations');
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->foreign('employer_id')
                  ->references('id')
                  ->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['course_class_id']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['discipline_id']);
        });

        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
        });

        Schema::table('phones', function (Blueprint $table) {
            $table->dropForeign(['registration_id']);
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['registration_id']);
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->dropForeign(['registration_id']);
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign('registrations_employer_id_foreign');
        });
    }
}
