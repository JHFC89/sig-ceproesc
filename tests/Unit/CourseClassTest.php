<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Holiday;
use App\Models\CourseClass;
use App\Models\Discipline;
use App\Exceptions\NotANoviceException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\CourseClassAlreadyHasLessonsException;

class CourseClassTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_all_months()
    {
        $courseClass = $this->testCourseClass();
        $courseClass->begin = Carbon::create(2021, 1, 16); 
        $courseClass->end = Carbon::create(2022, 6, 16); 
        $courseClass->intro_begin = Carbon::create(2021, 1, 16); 
        $courseClass->intro_end = Carbon::create(2021, 1, 30); 
        $courseClass->vacation_begin = Carbon::create(2022, 1, 17); 
        $courseClass->vacation_end = Carbon::create(2022, 2, 15); 
        $courseClass->save(); 

        $result = $courseClass->allMonths();

        $expectedResult = collect([
            ['month' => 1, 'year' => 2021, 'id' => '01-2021'],
            ['month' => 2, 'year' => 2021, 'id' => '02-2021'],
            ['month' => 3, 'year' => 2021, 'id' => '03-2021'],
            ['month' => 4, 'year' => 2021, 'id' => '04-2021'],
            ['month' => 5, 'year' => 2021, 'id' => '05-2021'],
            ['month' => 6, 'year' => 2021, 'id' => '06-2021'],
            ['month' => 7, 'year' => 2021, 'id' => '07-2021'],
            ['month' => 8, 'year' => 2021, 'id' => '08-2021'],
            ['month' => 9, 'year' => 2021, 'id' => '09-2021'],
            ['month' => 10, 'year' => 2021, 'id' => '10-2021'],
            ['month' => 11, 'year' => 2021, 'id' => '11-2021'],
            ['month' => 12, 'year' => 2021, 'id' => '12-2021'],
            ['month' => 1, 'year' => 2022, 'id' => '01-2022'],
            ['month' => 2, 'year' => 2022, 'id' => '02-2022'],
            ['month' => 3, 'year' => 2022, 'id' => '03-2022'],
            ['month' => 4, 'year' => 2022, 'id' => '04-2022'],
            ['month' => 5, 'year' => 2022, 'id' => '05-2022'],
            ['month' => 6, 'year' => 2022, 'id' => '06-2022'],
        ]);

        $this->assertEquals($expectedResult->count(), $result->count());
        $this->assertEquals($expectedResult->toArray(), $result->toArray());
    }

    /** @test */
    public function can_get_all_theoretical_activity_days()
    {
        $courseClass = $this->testCourseClass();
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

        $result = $courseClass->allTheoreticalDays();

        $expectedResult = collect([
            Carbon::create(2021, 4, 1),
            Carbon::create(2021, 4, 2),
            Carbon::create(2021, 4, 3),
            Carbon::create(2021, 4, 5),
            Carbon::create(2021, 4, 6),
            Carbon::create(2021, 4, 7),
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
    public function can_get_all_theoretical_activity_days_for_a_month()
    {
        $courseClass = $this->testCourseClass();
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

        $result = $courseClass->theoreticalDaysForMonth(5, 2021);

        $expectedResult = collect([
            Carbon::create(2021, 5, 21),
            Carbon::create(2021, 5, 22),
            Carbon::create(2021, 5, 28),
            Carbon::create(2021, 5, 29),
        ])->keyBy->format('d-m-Y');

        $this->assertEquals($expectedResult->keys(), $result->keys());
    }

    /** @test */
    public function can_get_total_duration_for_theoretical_activity_days()
    {
        $courseClass = $this->testCourseClass();
        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 4, 30); 
        $courseClass->intro_begin = Carbon::create(2021, 4, 1); 
        $courseClass->intro_end = Carbon::create(2021, 4, 7); 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
        $courseClass->save();

        $resultInHours = $courseClass->totalTheoreticalDaysDuration();

        $this->assertEquals(56, $resultInHours);
    }

    /** @test */
    public function can_get_all_practical_activity_days()
    {
        $courseClass = $this->testCourseClass();
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
        Holiday::factory()->create(['date' => Carbon::create(2021, 4, 21)]);
        Holiday::factory()->create(['date' => Carbon::create(2021, 5, 1),]);

        $result = $courseClass->allPracticalDays();

        $expectedResult = collect([
            Carbon::create(2021, 4, 8),
            Carbon::create(2021, 4, 12),
            Carbon::create(2021, 4, 13),
            Carbon::create(2021, 4, 14),
            Carbon::create(2021, 4, 15),
            Carbon::create(2021, 4, 19),
            Carbon::create(2021, 4, 20),
            Carbon::create(2021, 4, 22),
            Carbon::create(2021, 4, 26),
            Carbon::create(2021, 4, 27),
            Carbon::create(2021, 4, 28),
            Carbon::create(2021, 4, 29),
            Carbon::create(2021, 5, 18),
            Carbon::create(2021, 5, 19),
            Carbon::create(2021, 5, 20),
            Carbon::create(2021, 5, 24),
            Carbon::create(2021, 5, 25),
            Carbon::create(2021, 5, 26),
            Carbon::create(2021, 5, 27),
            Carbon::create(2021, 5, 31),
            Carbon::create(2021, 6, 1),
            Carbon::create(2021, 6, 2),
            Carbon::create(2021, 6, 3),
            Carbon::create(2021, 6, 7),
            Carbon::create(2021, 6, 8),
            Carbon::create(2021, 6, 9),
            Carbon::create(2021, 6, 10),
            Carbon::create(2021, 6, 14),
            Carbon::create(2021, 6, 15),
            Carbon::create(2021, 6, 16),
            Carbon::create(2021, 6, 17),
            Carbon::create(2021, 6, 21),
            Carbon::create(2021, 6, 22),
            Carbon::create(2021, 6, 23),
            Carbon::create(2021, 6, 24),
            Carbon::create(2021, 6, 28),
            Carbon::create(2021, 6, 29),
            Carbon::create(2021, 6, 30),
            Carbon::create(2021, 7, 1),
        ])->keyBy->format('d-m-Y');

        $this->assertEquals($expectedResult->keys(), $result->keys());
    }

    /** @test */
    public function can_get_total_duration_for_practical_activity_days()
    {
        $courseClass = $this->testCourseClass();
        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 4, 30); 
        $courseClass->intro_begin = Carbon::create(2021, 4, 1); 
        $courseClass->intro_end = Carbon::create(2021, 4, 7); 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
        $courseClass->practical_duration = 1; 
        $courseClass->save();

        $resultInMinutes = $courseClass->totalPracticalDaysDuration();

        $this->assertEquals(13, $resultInMinutes);
    }

    /** @test */
    public function can_get_all_offdays()
    {
        $courseClass = $this->testCourseClass();
        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 7, 30); 
        $courseClass->intro_begin = Carbon::create(2021, 4, 1); 
        $courseClass->intro_end = Carbon::create(2021, 4, 7); 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
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
        $courseClass = $this->testCourseClass();
        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 7, 30); 
        $courseClass->intro_begin = Carbon::create(2021, 4, 1); 
        $courseClass->intro_end = Carbon::create(2021, 4, 7); 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
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

    /** @test */
    public function can_check_a_novice_is_subscribed()
    {
        $noviceA = User::fakeNovice();
        $noviceB = User::fakeNovice();
        $courseClass = CourseClass::factory()->create();
        $courseClass->subscribe($noviceA);

        $resultA = $courseClass->isSubscribed($noviceA);
        $resultB = $courseClass->isSubscribed($noviceB);

        $this->assertTrue($resultA);
        $this->assertFalse($resultB);
    }

    /** @test */
    public function trying_to_check_the_subscription_of_an_user_that_is_not_a_novice_should_throw_a_exception()
    {
        $user = User::factory()->create();
        $courseClass = CourseClass::factory()->create();

        try {
            $courseClass->isSubscribed($user);
        } catch (NotANoviceException $exception) {
            $this->assertEquals(
                'Trying to check subscription for a user that is not a novice',
                $exception->getMessage()
            );
            return;
        }

        $this->fail('Trying to check subscription for a user that is not a novice should throw an exception');
    }

    /** @test */
    public function can_check_a_employer_has_novices_subscribed_to_a_course_class()
    {
        $employerA = User::fakeEmployer();
        $employerB = User::fakeEmployer();
        $noviceA = User::fakeNovice();
        $noviceB = User::fakeNovice();
        $courseClass = CourseClass::factory()->create();
        $employerA->novices()->save($noviceA);
        $employerB->novices()->save($noviceB);
        $courseClass->subscribe($noviceA);

        $resultA = $courseClass->hasNovicesFor($employerA);
        $resultB = $courseClass->hasNovicesFor($employerB);

        $this->assertTrue($resultA);
        $this->assertFalse($resultB);
    }

    /** @test */
    public function can_create_lessons_from_array()
    {
        $courseClass = $this->testCourseClass();
        $courseClass->save();
        $instructorA = User::fakeInstructor();
        $instructorB = User::fakeInstructor();
        $disciplineA = Discipline::factory()->create();
        $disciplineB = Discipline::factory()->create();
        $dateA = now()->addDays(1)->format('Y-m-d');
        $dateB = now()->addDays(2)->format('Y-m-d');
        $idA = $dateA . '-' . 'first';
        $idB = $dateB . '-' . 'second';
        $array = [
            $idA => [
                'id' => $idA,
                'date' => $dateA,
                'type' => 'first',
                'duration' => 2,
                'instructor_id' => $instructorA->id,
                'discipline_id' => $disciplineA->id,
            ],
            $idB => [
                'id' => $idB,
                'date' => $dateB,
                'type' => 'second',
                'duration' => 3,
                'instructor_id' => $instructorB->id,
                'discipline_id' => $disciplineB->id,
            ],
        ];

        $result = $courseClass->createLessonsFromArray($array);

        $lessonA = $result->first();
        $lessonB = $result->last();
        $this->assertInstanceOf(Lesson::class, $lessonA);
        $this->assertEquals($dateA, $lessonA->date->format('Y-m-d'));
        $this->assertEquals($courseClass->id, $lessonA->courseClasses->first()->id);
        $this->assertInstanceOf(Lesson::class, $lessonB);
        $this->assertEquals($dateB, $lessonB->date->format('Y-m-d'));
        $this->assertEquals($courseClass->id, $lessonB->courseClasses->first()->id);
    }

    /** @test */
    public function cannot_create_lessons_from_array_if_already_have_lessons()
    {
        $courseClass = $this->testCourseClass();
        $courseClass->save();
        $instructorA = User::fakeInstructor();
        $instructorB = User::fakeInstructor();
        $disciplineA = Discipline::factory()->create();
        $disciplineB = Discipline::factory()->create();
        $dateA = now()->addDays(1)->format('Y-m-d');
        $dateB = now()->addDays(2)->format('Y-m-d');
        $idA = $dateA . '-' . 'first';
        $idB = $dateB . '-' . 'second';
        $array = [
            $idA => [
                'id' => $idA,
                'date' => $dateA,
                'type' => 'first',
                'duration' => 2,
                'instructor_id' => $instructorA->id,
                'discipline_id' => $disciplineA->id,
            ],
            $idB => [
                'id' => $idB,
                'date' => $dateB,
                'type' => 'second',
                'duration' => 3,
                'instructor_id' => $instructorB->id,
                'discipline_id' => $disciplineB->id,
            ],
        ];
        $courseClass->createLessonsFromArray($array);
        $courseClass->refresh();

        try {
            $courseClass->createLessonsFromArray($array);
        } catch (CourseClassAlreadyHasLessonsException $exception) {
            $this->assertEquals(
                'Trying to create Lessons for a CourseClass that already have Lessons.',
                $exception->getMessage()
            );
            return;
        }

        $this->fail('Trying to create Lessons for a CourseClass that already have Lessons should throw an exception.');
    }

    private function testCourseClass()
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

        return $courseClass;
    }
}
