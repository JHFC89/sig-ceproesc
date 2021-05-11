<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->index();
        });

        Role::create(['name' => Role::ADMIN]);
        Role::create(['name' => Role::COORDINATOR]);
        Role::create(['name' => Role::INSTRUCTOR]);
        Role::create(['name' => Role::NOVICE]);
        Role::create(['name' => Role::EMPLOYER]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
