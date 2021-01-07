<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRectifyLessonRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rectify_lesson_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id');
            $table->text('justification');
            $table->dateTime('released_at')->nullable();
            $table->dateTime('solved_at')->nullable();
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
        Schema::dropIfExists('rectify_lesson_requests');
    }
}
