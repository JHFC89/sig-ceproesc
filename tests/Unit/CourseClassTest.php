<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Holiday;
use App\Models\CourseClass;
use App\Exceptions\NotANoviceException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseClassTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_all_theoretical_activity_days()
    {
        $courseClass = new CourseClass;
        $courseClass->name = 'test name'; 
        $courseClass->city = 'fake city'; 
        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 7, 1); 
        $courseClass->first_theoretical_activity_day = 'friday'; 
        $courseClass->first_theoretical_activity_duration = 4; 
        $courseClass->second_theoretical_activity_day = 'saturday'; 
        $courseClass->second_theoretical_activity_duration = 4; 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
        $courseClass->course_id = 1; 
        $courseClass->save(); 
        $courseClass->offdays()->createMany([
            ['date' => Carbon::create(2021, 4, 30)],
            ['date' => Carbon::create(2021, 6, 11)],
        ]);
        Holiday::factory()->create([
            'date' => Carbon::create(2021, 5, 1),
        ]);

        $result = $courseClass->allTheoreticalDays();

        $expectedResult = collect([
            Carbon::create(2021, 4, 2),
            Carbon::create(2021, 4, 3),
            Carbon::create(2021, 4, 9),
            Carbon::create(2021, 4, 10),
            Carbon::create(2021, 4, 16),
            Carbon::create(2021, 4, 17),
            Carbon::create(2021, 4, 23),
            Carbon::create(2021, 4, 24),
            Carbon::create(2021, 5, 21),
            Carbon::create(2021, 5, 22),
            Carbon::create(2021, 5, 28),
            Carbon::create(2021, 5, 29),
            Carbon::create(2021, 6, 4),
            Carbon::create(2021, 6, 5),
            Carbon::create(2021, 6, 12),
            Carbon::create(2021, 6, 18),
            Carbon::create(2021, 6, 19),
            Carbon::create(2021, 6, 25),
            Carbon::create(2021, 6, 26),
        ])->keyBy->format('d-m-Y');

        $this->assertEquals($expectedResult->keys(), $result->keys());
    }

    /** @test */
    public function can_get_total_duration_for_theoretical_activity_days()
    {
        $courseClass = new CourseClass;
        $courseClass->name = 'test name'; 
        $courseClass->city = 'test city'; 
        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 4, 30); 
        $courseClass->first_theoretical_activity_day = 'friday'; 
        $courseClass->first_theoretical_activity_duration = 4; 
        $courseClass->second_theoretical_activity_day = 'saturday'; 
        $courseClass->second_theoretical_activity_duration = 5; 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
        $courseClass->course_id = 1;
        $courseClass->save(); 

        $result = $courseClass->totalTheoreticalDaysDuration();

        $this->assertEquals(40, $result);
    }

    /** @test */
    public function can_get_all_offdays()
    {
        $courseClass = new CourseClass;
        $courseClass->name = 'test name'; 
        $courseClass->city = 'test city'; 
        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 7, 30); 
        $courseClass->first_theoretical_activity_day = 'friday'; 
        $courseClass->first_theoretical_activity_duration = 4; 
        $courseClass->second_theoretical_activity_day = 'saturday'; 
        $courseClass->second_theoretical_activity_duration = 5; 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
        $courseClass->course_id = 1;
        $courseClass->save(); 
        $courseClass->offdays()->createMany([
            ['date' => Carbon::create(2021, 4, 30)],
            ['date' => Carbon::create(2021, 6, 11)],
        ]);

        $result = $courseClass->allOffdays();

        $expectedResult = collect([
            Carbon::create(2021, 4, 30),
            Carbon::create(2021, 6, 11),
        ])->keyBy->format('d-m-Y');

        $this->assertEquals($expectedResult->keys(), $result->keys());
    }

    /** @test */
    public function can_get_all_vacation_days()
    {
        $courseClass = new CourseClass;
        $courseClass->name = 'test name'; 
        $courseClass->city = 'test city'; 
        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 7, 30); 
        $courseClass->first_theoretical_activity_day = 'friday'; 
        $courseClass->first_theoretical_activity_duration = 4; 
        $courseClass->second_theoretical_activity_day = 'saturday'; 
        $courseClass->second_theoretical_activity_duration = 5; 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
        $courseClass->course_id = 1;
        $courseClass->save(); 

        $result = $courseClass->allVacationDays();

        $expectedResult = collect([
            Carbon::create(2021, 5, 3),
            Carbon::create(2021, 5, 4),
            Carbon::create(2021, 5, 5),
            Carbon::create(2021, 5, 6),
            Carbon::create(2021, 5, 7),
            Carbon::create(2021, 5, 8),
            Carbon::create(2021, 5, 9),
            Carbon::create(2021, 5, 10),
            Carbon::create(2021, 5, 11),
            Carbon::create(2021, 5, 12),
            Carbon::create(2021, 5, 13),
            Carbon::create(2021, 5, 14),
            Carbon::create(2021, 5, 15),
            Carbon::create(2021, 5, 16),
            Carbon::create(2021, 5, 17),
        ])->keyBy->format('d-m-Y');

        $this->assertEquals($expectedResult->keys(), $result->keys());
    }

    /** @test */
    public function subscribe_novice_to_course_class()
    {
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();
        $courseClass = CourseClass::factory()->create(['name' => 'julho - 2020']);

        $courseClass->subscribe($novice);

        $this->assertEquals('julho - 2020', $novice->class);
    }

    /** @test */
    public function subscribe_a_user_that_is_not_a_novice_should_throw_a_exception()
    {
        $user = User::factory()->create();
        $courseClass = CourseClass::factory()->create();

        try {
            $courseClass->subscribe($user);
        } catch (NotANoviceException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to subscribe a user that is not a novice to a course class should throw an exception');
    }
}
