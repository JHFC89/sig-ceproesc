<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id');
            $table->string('name');
            $table->string('city');
            $table->date('begin');
            $table->date('end');
            $table->date('intro_begin');
            $table->date('intro_end');
            $table->string('first_theoretical_activity_day');
            $table->string('second_theoretical_activity_day');
            $table->integer('first_theoretical_activity_duration');
            $table->integer('second_theoretical_activity_duration');
            $table->integer('practical_duration');
            $table->date('vacation_begin');
            $table->date('vacation_end');
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
        Schema::dropIfExists('course_classes');
    }
}
