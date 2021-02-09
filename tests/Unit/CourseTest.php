<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Course;
use App\Models\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\InvalidDisciplinesDurationException;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_add_disciplines()
    {
        $disciplines = Discipline::factory()
            ->count(10)
            ->create(['duration' => 10]);
        $course = Course::factory()->create(['duration' => 100]);

        $course->addDisciplines($disciplines->pluck('id')->toArray());

        $this->assertEquals(10, $course->disciplines->count());
    }


    /** @test */
    public function cannot_add_disciplines_if_the_total_duration_in_not_equal_to_course_duration()
    {
        $disciplines = Discipline::factory()
            ->count(10)
            ->create(['duration' => 10]);
        $course = Course::factory()->create(['duration' => 50]);

        try {
            $course->addDisciplines($disciplines->pluck('id')->toArray());
        } catch (InvalidDisciplinesDurationException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to add disciplines with invalid duration should throw an exception');
    }

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
