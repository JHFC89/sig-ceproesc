<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Discipline;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Course::factory()
            ->hasAttached(
                Discipline::factory()
                    ->count(10)
                    ->state(new Sequence(
                        ['basic' => true],
                        ['basic' => false]
                    ))
            )
            ->create();
    }
}
