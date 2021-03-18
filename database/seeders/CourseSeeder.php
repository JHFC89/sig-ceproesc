<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Discipline;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $disciplinesA = Discipline::where('duration', 30)->take(2)->get();
        $disciplinesB = Discipline::where('duration', 48)->take(2)->get();
        $disciplines = $disciplinesA->concat($disciplinesB);

        $course = Course::factory()->create([
            'name'      => 'Programa de Teste',
            'duration'  => 156,
        ]);

        $course->addDisciplines($disciplines->pluck('id')->toArray());
    }
}
