<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AprendizForm;

class AddsVagaAndProgramaFieldsToAprendizFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aprendiz_forms', function (Blueprint $table) {
            $table->string('vaga', 255)->nullable()->index();
            $table->enum('programa', AprendizForm::PROGRAMAS)->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aprendiz_forms', function (Blueprint $table) {
            //
        });
    }
}
