<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Course;
use App\Models\CourseClass;
use Illuminate\Database\Seeder;

class CourseClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $courseClassA = CourseClass::factory()->hasNovices(10)->make([
            'name' => 'janeiro - 2020'
        ]);

        $courseClassB = CourseClass::factory()->hasNovices(10)->make([
            'name' => 'julho - 2020'
        ]);

        $courseClassC = CourseClass::factory()->hasNovices(10)->make([
            'name' => 'janeiro - 2021'
        ]);

        Course::first()->courseClasses()->saveMany([
            $courseClassA,
            $courseClassB,
            $courseClassC,
        ]);

        $courseClassA->offdays()->createMany([
            ['date' => Carbon::create(2021, 3, 19)],
            ['date' => Carbon::create(2021, 4, 30)],
        ]);
    }
}
