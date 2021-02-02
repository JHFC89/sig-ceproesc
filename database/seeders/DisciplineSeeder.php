<?php

namespace Database\Seeders;

use App\Models\Discipline;
use Illuminate\Database\Seeder;

class DisciplineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $discipline = Discipline::factory()->hasInstructors(3)->create();
        $discipline->instructors->each(function ($instructor) {
            $instructor->turnIntoInstructor();
        });
    }
}
