<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Course;
use App\Models\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_basic_disciplines()
    {
        $course = Course::factory()
            ->hasAttached(
                Discipline::factory()
                    ->count(3)
                    ->basic()
            )
            ->create();

        $result = $course->basicDisciplines();

        $this->assertEquals(3, $result->count());
    }

    /** @test */
    public function can_get_specific_disciplines()
    {
        $course = Course::factory()
            ->hasAttached(
                Discipline::factory()
                    ->count(3)
                    ->specific()
            )
            ->create();

        $result = $course->specificDisciplines();

        $this->assertEquals(3, $result->count());
    }

    /** @test */
    public function can_get_basic_disciplines_total_duration()
    {
        $course = Course::factory()
            ->hasAttached(
                Discipline::factory()
                    ->count(3)
                    ->basic()
                    ->state( function (array $attributes) {
                        return ['duration' => 15];
                    })
            )
            ->create();

        $result = $course->basicDisciplinesDuration();

        $this->assertEquals(45, $result);
    }
}
