<?php

namespace Database\Seeders;

use App\Models\Holiday;
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
        $courseClass = new CourseClass;
        $courseClass->name = 'test name'; 
        $courseClass->city = 'fake city'; 
        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 7, 1); 
        $courseClass->intro_begin = Carbon::create(2021, 4, 1); 
        $courseClass->intro_end = Carbon::create(2021, 4, 7); 
        $courseClass->first_theoretical_activity_day = 'friday'; 
        $courseClass->first_theoretical_activity_duration = 4; 
        $courseClass->second_theoretical_activity_day = 'saturday'; 
        $courseClass->second_theoretical_activity_duration = 5; 
        $courseClass->practical_duration = 1; 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
        $courseClass->course_id = 1; 

        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 7, 1); 
        $courseClass->intro_begin = Carbon::create(2021, 4, 1); 
        $courseClass->intro_end = Carbon::create(2021, 4, 7); 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
        $courseClass->save(); 
        $courseClass->offdays()->createMany([
            ['date' => Carbon::create(2021, 4, 30)],
            ['date' => Carbon::create(2021, 6, 11)],
        ]);
        Holiday::factory()->create([
            'date' => Carbon::create(2021, 5, 1),
        ]);
        $courseClass->extraLessonDays()->createMany([
            ['date' => Carbon::create(2021, 4, 17)],
            ['date' => Carbon::create(2021, 6, 5)],
        ]);

        $courseClass->save();
    }
}
