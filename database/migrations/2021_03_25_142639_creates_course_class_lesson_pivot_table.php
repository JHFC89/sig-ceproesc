<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesCourseClassLessonPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_class_lesson', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained();
            $table->foreignId('lesson_id');
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
        Schema::table('course_class_lesson', function (Blueprint $table) {
            $table->dropForeign(['course_class_id']);
        });

        Schema::dropIfExists('course_class_lesson');
    }
}
