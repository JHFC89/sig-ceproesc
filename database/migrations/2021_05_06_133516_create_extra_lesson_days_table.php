<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraLessonDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_lesson_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained();
            $table->date('date');
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
        Schema::table('extra_lesson_days', function (Blueprint $table) {
            $table->dropForeign(['course_class_id']);
        });

        Schema::dropIfExists('extra_lesson_days');
    }
}
