<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
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
        $courseClass->course_id = Course::factory()->create()->id;
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
    public function can_get_all_theoretical_activity_days_without_extra_lessons()
    {
        $courseClass = $this->testCourseClass();
        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 7, 1); 
        $courseClass->intro_begin = Carbon::create(2021, 4, 1); 
        $courseClass->intro_end = Carbon::create(2021, 4, 7); 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
        $courseClass->course_id = Course::factory()->create()->id;
        $courseClass->save(); 
        $courseClass->offdays()->createMany([
            ['date' => Carbon::create(2021, 4, 30)],
            ['date' => Carbon::create(2021, 6, 11)],
        ]);
        Holiday::factory()->create([
            'date' => Carbon::create(2021, 5, 1),
        ]);
        $courseClass->extraLessonDays()->createMany([
            ['date' => Carbon::create(2021, 4, 5)],
            ['date' => Carbon::create(2021, 6, 5)],
        ]);

        $result = $courseClass->allTheoreticalDays();

        $expectedResult = collect([
            Carbon::create(2021, 4, 1),
            Carbon::create(2021, 4, 2),
            Carbon::create(2021, 4, 3),
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
            Carbon::create(2021, 6, 12),
            Carbon::create(2021, 6, 18),
            Carbon::create(2021, 6, 19),
            Carbon::create(2021, 6, 25),
            Carbon::create(2021, 6, 26),
        ])->keyBy->format('d-m-Y');

        $this->assertEquals($expectedResult->keys(), $result->keys());
    }

    /** @test */
    public function can_get_all_theoretical_activity_days_with_extra_lessons()
    {
        $courseClass = $this->testCourseClass();
        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 7, 1); 
        $courseClass->intro_begin = Carbon::create(2021, 4, 1); 
        $courseClass->intro_end = Carbon::create(2021, 4, 7); 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
        $courseClass->course_id = Course::factory()->create()->id;
        $courseClass->save(); 
        $courseClass->offdays()->createMany([
            ['date' => Carbon::create(2021, 4, 30)],
            ['date' => Carbon::create(2021, 6, 11)],
        ]);
        Holiday::factory()->create([
            'date' => Carbon::create(2021, 5, 1),
        ]);
        $courseClass->extraLessonDays()->createMany([
            ['date' => Carbon::create(2021, 4, 5)],
            ['date' => Carbon::create(2021, 6, 5)],
        ]);

        $result = $courseClass->allTheoreticalDays(false);

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
        $courseClass->course_id = Course::factory()->create()->id;
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
        $courseClass->course_id = Course::factory()->create()->id;
        $courseClass->save();

        $resultInMinutes = $courseClass->totalTheoreticalDaysDuration();

        $this->assertEquals(56, $resultInMinutes);
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
        $courseClass->course_id = Course::factory()->create()->id;
        $courseClass->save(); 
        $courseClass->offdays()->createMany([
            ['date' => Carbon::create(2021, 6, 1)],
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
        $courseClass->course_id = Course::factory()->create()->id;
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
        $courseClass->course_id = Course::factory()->create()->id;
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
    public function can_get_all_extra_lesson_days()
    {
        $courseClass = $this->testCourseClass();
        $courseClass->begin = Carbon::create(2021, 4, 1); 
        $courseClass->end = Carbon::create(2021, 7, 30); 
        $courseClass->intro_begin = Carbon::create(2021, 4, 1); 
        $courseClass->intro_end = Carbon::create(2021, 4, 7); 
        $courseClass->vacation_begin = Carbon::create(2021, 5, 3); 
        $courseClass->vacation_end = Carbon::create(2021, 5, 17); 
        $courseClass->course_id = Course::factory()->create()->id;
        $courseClass->save(); 
        $courseClass->extraLessonDays()->createMany([
            ['date' => Carbon::create(2021, 4, 30)],
            ['date' => Carbon::create(2021, 6, 11)],
        ]);

        $result = $courseClass->allExtraLessonDays();

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
        $courseClass->course_id = Course::factory()->create()->id;
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
    public function subscribing_a_novice_will_enroll_him_in_all_course_class_lessons()
    {
        $courseClass = $this->testCourseClassWithLessons();
        $lessons = $courseClass->lessons;
        $this->assertCount(2, $lessons);
        $novice = User::fakeNovice();

        $courseClass->subscribe($novice);

        $novice->refresh();
        $this->assertCount(2, $novice->lessons);
        $this->assertTrue($novice->lessons[0]->is($lessons[0]));
        $this->assertTrue($novice->lessons[1]->is($lessons[1]));
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
        $employerA->company->novices()->save($noviceA->registration);
        $employerB->company->novices()->save($noviceB->registration);
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
        $array = [
            [
                'id' => 'lesson A',
                'date' => $dateA,
                'type' => 'first',
                'duration' => 2,
                'instructor_id' => $instructorA->id,
                'discipline_id' => $disciplineA->id,
            ],
            [
                'id' => 'Lesson B',
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
    public function duplicate_lessons_will_be_joined_when_creating_from_array()
    {
        $courseClassA = $this->testCourseClass();
        $courseClassA->name = 'Class A';
        $courseClassA->save();
        $courseClassB = $this->testCourseClass();
        $courseClassB->name = 'Class B';
        $courseClassB->save();
        $instructorA = User::fakeInstructor();
        $instructorB = User::fakeInstructor();
        $disciplineA = Discipline::factory()->create();
        $disciplineB = Discipline::factory()->create();
        $dateA = $courseClassA->begin->addDays(1)->format('Y-m-d');
        $dateB = $courseClassA->begin->addDays(2)->format('Y-m-d');
        $dateC = $courseClassA->begin->addDays(3)->format('Y-m-d');
        $dateD = $courseClassA->begin->addDays(4)->format('Y-m-d');
        $arrayA = [
            [
                'id' => 'Lesson A',
                'date' => $dateA,
                'type' => 'first',
                'duration' => 2,
                'instructor_id' => $instructorA->id,
                'discipline_id' => $disciplineA->id,
            ],
            [
                'id' => 'Lesson B',
                'date' => $dateB,
                'type' => 'second',
                'duration' => 3,
                'instructor_id' => $instructorB->id,
                'discipline_id' => $disciplineB->id,
            ],
            [
                'id' => 'Lesson C',
                'date' => $dateC,
                'type' => 'second',
                'duration' => 2,
                'instructor_id' => $instructorA->id,
                'discipline_id' => $disciplineB->id,
            ],
            [
                'id' => 'Lesson D',
                'date' => $dateD,
                'type' => 'second',
                'duration' => 3,
                'instructor_id' => $instructorB->id,
                'discipline_id' => $disciplineA->id,
            ],
        ];
        $courseClassA->createLessonsFromArray($arrayA);
        $this->assertEquals(4, Lesson::count());
        //Lessons B and C are equal to Class A's Lessons B and C
        $arrayB = [
            [
                'id' => 'Lesson A',
                'date' => $dateA,
                'type' => 'second',
                'duration' => 2,
                'instructor_id' => $instructorB->id,
                'discipline_id' => $disciplineB->id,
            ],
            [
                'id' => 'Lesson B',
                'date' => $dateB,
                'type' => 'second',
                'duration' => 3,
                'instructor_id' => $instructorB->id,
                'discipline_id' => $disciplineB->id,
            ],
            [
                'id' => 'Lesson C',
                'date' => $dateC,
                'type' => 'second',
                'duration' => 2,
                'instructor_id' => $instructorA->id,
                'discipline_id' => $disciplineB->id,
            ],
            [
                'id' => 'Lesson D',
                'date' => $dateD,
                'type' => 'first',
                'duration' => 3,
                'instructor_id' => $instructorA->id,
                'discipline_id' => $disciplineB->id,
            ],
        ];

        $courseClassB->createLessonsFromArray($arrayB);

        $this->assertEquals(6, Lesson::count());
        $this->assertEquals(4, $courseClassA->lessons()->count());
        $this->assertEquals(4, $courseClassB->lessons()->count());
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
        $array = [
            [
                'id' => 'Lesson A',
                'date' => $dateA,
                'type' => 'first',
                'duration' => 2,
                'instructor_id' => $instructorA->id,
                'discipline_id' => $disciplineA->id,
            ],
            [
                'id' => 'Lesson B',
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

    /** @test */
    public function can_get_a_novice_frequency()
    {
        $lessons = Lesson::factory()->duration(2 * 60)->count(5);
        $courseClass = CourseClass::factory()->hasNovices(1)->has($lessons)->create();
        $novice = $courseClass->novices->first()->turnIntoNovice()->refresh();
        $courseClass->subscribe($novice);
        $courseClass->lessons->each(function ($lesson, $key) use ($novice) {
            if ($key == 4) {
                $lesson->registerFor($novice)->absent()->complete()->register();

                return;
            }

            $lesson->registerFor($novice)->present()->complete()->register();
        });
        $this->assertCount(5, $courseClass->lessons);
        $this->assertEquals(10 * 60, $courseClass->lessons->sum('hourly_load'));
        $this->assertTrue($courseClass->isSubscribed($novice));
        $this->assertTrue($courseClass->lessons->every->isEnrolled($novice));
        $this->assertTrue($courseClass->lessons->every->isRegistered());
        
        $result = $courseClass->noviceFrequency($novice);

        $this->assertEquals('80,00', $result);
    }

    /** @test */
    public function novice_frequency_must_ignore_extra_lessons()
    {
        $lessons = Lesson::factory()->duration(2)->count(5);
        $courseClass = CourseClass::factory()->hasNovices(1)
                                             ->has($lessons)
                                             ->create();
        $novice = $courseClass->novices->first()->turnIntoNovice()->refresh();
        $courseClass->subscribe($novice);

        // novice only present in the first lesson but have 100% frequency 
        // because the others are extra lessons
        $date = now()->subDays(3);
        $courseClass->lessons[0]->update(['date' => $date]);
        $courseClass->lessons[0]->registerFor($novice)
                                ->present()
                                ->complete()
                                ->register();
        $courseClass->lessons[1]->update(['date' => $date->addDay()]);
        $courseClass->lessons[1]->registerFor($novice)
                                ->absent()
                                ->complete()
                                ->register();
        $courseClass->lessons[2]->update(['date' => $date->addDay()]);
        $courseClass->lessons[2]->registerFor($novice)
                                ->absent()
                                ->complete()
                                ->register();
        $courseClass->lessons[3]->update(['date' => $date->addDay()]);
        $courseClass->lessons[3]->registerFor($novice)
                                ->absent()
                                ->complete()
                                ->register();
        $courseClass->lessons[4]->update(['date' => $date->addDay()]);
        $courseClass->lessons[4]->registerFor($novice)
                                ->absent()
                                ->complete()
                                ->register();
        $this->assertCount(5, $courseClass->lessons);
        $this->assertEquals(10, $courseClass->lessons->sum('hourly_load'));
        $this->assertTrue($courseClass->isSubscribed($novice));
        $this->assertTrue($courseClass->lessons->every->isEnrolled($novice));
        $this->assertTrue($courseClass->lessons->every->isRegistered());

        // make the last 4 lessons be extra lessons
        $lessons = $courseClass->lessons;
        $courseClass->extraLessonDays()->createMany([
            ['date' => $lessons[1]->date->toString()],
            ['date' => $lessons[2]->date->toString()],
            ['date' => $lessons[3]->date->toString()],
            ['date' => $lessons[4]->date->toString()],
        ]);
        
        $result = $courseClass->noviceFrequency($novice);

        $this->assertEquals('100,00', $result);
    }

    /** @test */
    public function novice_frequency_must_return_false_if_there_is_no_registered_lesson()
    {
        $lessons = Lesson::factory()->duration(2)->count(5);
        $courseClass = CourseClass::factory()->hasNovices(1)->has($lessons)->create();
        $novice = $courseClass->novices->first()->turnIntoNovice()->refresh();
        $courseClass->subscribe($novice);
        $this->assertCount(5, $courseClass->lessons);
        $this->assertTrue($courseClass->lessons->every->isEnrolled($novice));
        $this->assertFalse($courseClass->lessons->every->isRegistered());
        
        $result = $courseClass->noviceFrequency($novice);

        $this->assertFalse($result);
    }

    /** @test */
    public function can_get_the_course_class_instructors()
    {
        $courseClass = $this->testCourseClassWithLessons();
        $lessons = $courseClass->lessons;

        $instructors = $courseClass->instructors(); 

        $this->assertTrue($lessons[0]->instructor->is($instructors[0]));
        $this->assertTrue($lessons[1]->instructor->is($instructors[1]));
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
        $courseClass->course_id = Course::factory()->create()->id;

        return $courseClass;
    }

    private function testCourseClassWithLessons()
    {
        $instructorA = User::fakeInstructor();
        $instructorB = User::fakeInstructor();

        $disciplineA = Discipline::factory()->create();
        $disciplineB = Discipline::factory()->create();

        $dateA = now()->addDays(1)->format('Y-m-d');
        $dateB = now()->addDays(2)->format('Y-m-d');

        $lessonsArray = [
            [
                'id' => 'lesson A',
                'date' => $dateA,
                'type' => 'first',
                'duration' => 2,
                'instructor_id' => $instructorA->id,
                'discipline_id' => $disciplineA->id,
            ],
            [
                'id' => 'Lesson B',
                'date' => $dateB,
                'type' => 'second',
                'duration' => 3,
                'instructor_id' => $instructorB->id,
                'discipline_id' => $disciplineB->id,
            ],
        ];

        $courseClass = CourseClass::factory()->create();
        $courseClass->createLessonsFromArray($lessonsArray);

        return $courseClass->fresh();
    }
}
