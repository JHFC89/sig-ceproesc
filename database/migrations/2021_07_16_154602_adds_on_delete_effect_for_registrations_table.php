<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsOnDeleteEffectForRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phones', function (Blueprint $table) {
            $table->dropForeign('phones_registration_id_foreign');
            $table->foreign('registration_id')
                ->references('id')
                ->on('registrations')
                ->onDelete('cascade');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign('addresses_registration_id_foreign');
            $table->foreign('registration_id')
                ->references('id')
                ->on('registrations')
                ->onDelete('cascade');
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->dropForeign('invitations_registration_id_foreign');
            $table->foreign('registration_id')
                ->references('id')
                ->on('registrations')
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
        Schema::table('registrations', function (Blueprint $table) {
            //
        });
    }
}
