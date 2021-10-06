<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForeignKeyConstraitsForSurveyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('survey.database.tables.answers'), function (Blueprint $table) {
            $table->foreign('question_id')
                ->references('id')
                ->on(config('survey.database.tables.questions'))
                ->onDelete('cascade');

            $table->foreign('entry_id')
                ->references('id')
                ->on(config('survey.database.tables.entries'))
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foreign_key_constraits_for_survey_tables');
    }
}
