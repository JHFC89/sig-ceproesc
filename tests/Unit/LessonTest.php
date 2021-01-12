<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\CourseClass;
use App\Exceptions\LessonRegisteredException;
use App\Exceptions\NoviceNotEnrolledException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LessonTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function can_get_the_formatted_date()
    {
        $lesson = Lesson::factory()->make([
            'date' => Carbon::parse('2020/11/02'),
        ]);

        $date = $lesson->formatted_date;

        $this->assertEquals('02/11/2020', $date);
    }

    /** @test */
    public function can_check_if_it_is_registered()
    {
        $lesson = Lesson::factory()->make([
            'register' => 'Fake register.',
            'registered_at' => Carbon::parse('+2 weeks'),
        ]);

        $this->assertTrue($lesson->isRegistered());
        $this->assertNotNull($lesson->registered_at);
    }

    /** @test */
    public function can_check_if_it_is_not_registered()
    {
        $lesson = Lesson::factory()->make([
            'registered_at' => null,
        ]);

        $this->assertFalse($lesson->isRegistered());
        $this->assertNull($lesson->registered_at);
    }

    /** @test */
    public function can_check_if_it_is_for_today()
    {
        $lesson = Lesson::factory()->forToday()->make([]);

        $this->assertTrue($lesson->isForToday());
    }

    /** @test */
    public function can_check_if_it_is_not_for_today()
    {
        $lesson = Lesson::factory()->notForToday()->make([]);

        $this->assertFalse($lesson->isForToday());
    }

    /** @test */
    public function can_check_assigned_instructor()
    {
        $lesson = Lesson::factory()->forInstructor()->create();
        $assignedInstructor = $lesson->instructor;
        $notAssignedInstructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $resultForAssignedInstructor = $lesson->isForInstructor($assignedInstructor);
        $resultForNotAssignedInstructor = $lesson->isForInstructor($notAssignedInstructor);
        
        $this->assertTrue($resultForAssignedInstructor);
        $this->assertFalse($resultForNotAssignedInstructor);
    }

    /** @test */
    public function can_check_if_its_expired()
    {
        $expiredDate = Carbon::now()->subHours(24)->subSecond();
        $expiredLesson = Lesson::factory()->notRegistered()->create(['date' => $expiredDate]);
        $notExpiredLessonForNow = Lesson::factory()->notRegistered()->create(['date' => Carbon::now()]);
        $notExpiredLessonForFuture = Lesson::factory()->notRegistered()->create(['date' => Carbon::parse('+2 weeks')]);
        $registeredLesson = Lesson::factory()->create(['registered_at' => $expiredDate]);

        $resultForExpiredLesson = $expiredLesson->isExpired();
        $resultForNotExpiredLessonForNow = $notExpiredLessonForNow->isExpired();
        $resultForNotExpiredLessonForFuture = $notExpiredLessonForFuture->isExpired();
        $resultForRegisteredLesson = $registeredLesson->isExpired();
        
        $this->assertTrue($resultForExpiredLesson);
        $this->assertFalse($resultForNotExpiredLessonForNow);
        $this->assertFalse($resultForNotExpiredLessonForFuture);
        $this->assertFalse($resultForRegisteredLesson);
    }

    /** @test */
    public function can_check_it_has_an_open_request_to_register()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();

        $result = $lesson->hasOpenRequest();

        $this->assertTrue($result);
    }

    /** @test */
    public function can_check_it_has_an_open_request_to_rectify()
    {
        $lesson = Lesson::factory()->registered()->hasRequests(1)->create();

        $result = $lesson->hasOpenRequest();

        $this->assertTrue($result);
    }

    /** @test */
    public function can_check_it_does_not_have_an_open_request_to_register()
    {
        $lesson = Lesson::factory()->expired()->create();

        $result = $lesson->hasOpenRequest();

        $this->assertFalse($result);
    }

    /** @test */
    public function a_lesson_with_a_released_request_does_not_have_an_open_request()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $lesson->openRequest()->release();

        $result = $lesson->hasOpenRequest();

        $this->assertFalse($result);
    }

    /** @test */
    public function get_open_request_to_register()
    {
        $lesson = Lesson::factory()->expired()->create();
        $request = $lesson->requests()->create(['justification' => 'test justification']);

        $result = $lesson->openRequest();

        $this->assertEquals($request->id, $result->id);
    }

    /** @test */
    public function get_open_request_to_rectify()
    {
        $lesson = Lesson::factory()->registered()->create();
        $request = $lesson->requests()->create(['justification' => 'test justification']);

        $result = $lesson->openRequest();

        $this->assertEquals($request->id, $result->id);
    }

    /** @test */
    public function can_check_it_has_a_pending_request_to_register()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $lesson->openRequest()->release();

        $result = $lesson->hasPendingRequest();

        $this->assertTrue($result);
    }

    /** @test */
    public function can_check_it_has_a_pending_request_to_rectify()
    {
        $lesson = Lesson::factory()->registered()->hasRequests(1)->create();
        $lesson->openRequest()->release();

        $result = $lesson->hasPendingRequest();

        $this->assertTrue($result);
    }

    /** @test */
    public function can_check_it_does_not_have_a_pending_request_when_has_an_open_request_to_register()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $lesson->openRequest();

        $result = $lesson->hasPendingRequest();

        $this->assertFalse($result);
    }

    /** @test */
    public function can_check_it_does_not_have_a_pending_request_when_has_an_open_request_to_rectify()
    {
        $lesson = Lesson::factory()->registered()->hasRequests(1)->create();
        $lesson->openRequest();

        $result = $lesson->hasPendingRequest();

        $this->assertFalse($result);
    }

    /** @test */
    public function get_pending_request()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();
        $request->release();

        $result = $lesson->pendingRequest();

        $this->assertEquals($request->id, $result->id);
    }

    /** @test */
    public function can_check_it_does_not_have_a_pending_request_when_solved()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();
        $request->release();
        $lesson->register();
        $request->solve($lesson);

        $result = $lesson->hasPendingRequest();

        $this->assertFalse($result);
    }

    /** @test */
    public function can_check_it_has_a_solved_request()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();
        $request->release();
        $lesson->register();
        $request->solve($lesson);

        $result = $lesson->hasSolvedRequest();

        $this->assertTrue($result);
    }

    /** @test */
    public function solve_any_pending_request_when_registering()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();
        $request->release();
        $lesson->register();

        $result = $request->fresh()->isSolved();

        $this->assertTrue($result);
    }

    /** @test */
    public function the_date_is_saved_as_utc_timezone()
    {
        $this->markTestSkipped();
        $date = now()->tz('America/Sao_Paulo')->format('d/m/Y H:i');
        $date = Carbon::createFromFormat('d/m/Y H:i', $date, 'America/Sao_Paulo');

        $lesson = Lesson::factory()->create([
            'date' => $date
        ]);

        $date->setTimezone('UTC');
        $this->assertEquals($date->format('d/m/Y H:i'), $lesson->date->format('d/m/Y H:i'));
    }
    
    /** @test */
    public function can_enroll_a_novice()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();
        
        $lesson->enroll($novice);

        $this->assertNotNull($novice->lessons);
        $this->assertEquals($lesson->id, $novice->lessons->first()->id);
    }

    /** @test */
    public function can_check_a_novice_is_enrolled()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();
        
        $lesson->enroll($novice);

        $this->assertTrue($lesson->isEnrolled($novice));
    }

    /** @test */
    public function can_check_a_novice_is_not_enrolled()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();
        
        $this->assertFalse($lesson->isEnrolled($novice));
    }

    /** @test */
    public function cannot_enroll_a_novice_if_lesson_is_already_registered()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->registered()->create();

        try {
            $lesson->enroll($novice);
        } catch (LessonRegisteredException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to enroll a novice to a lesson already registered should throw an exception');
    }

    /** @test */
    public function can_register_the_presence_of_an_enrolled_novice()
    {
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();

        $lesson->registerFor($novice)->present()->complete();

        $this->assertTrue($novice->lessons->first()->presence->present === 1);
    }

    /** @test */
    public function can_register_the_absence_of_an_enrolled_novice()
    {
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();

        $lesson->registerFor($novice)->absent()->complete();

        $this->assertTrue($novice->lessons->first()->presence->present === 0);
    }

    /** @test */
    public function cannot_register_the_presence_of_a_novice_that_is_not_enrolled()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();

        try {
            $lesson->registerPresence($novice)->present();
        } catch (NoviceNotEnrolledException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to register the presence of a novice that is not enrolled to the lesson should throw a exception');
    }

    /** @test */
    public function can_check_the_presence_of_a_novice()
    {
        $lesson = Lesson::factory()->hasNovices(2)->create();
        $presentNovice = $lesson->novices->first();
        $absentNovice = $lesson->novices->last();
        $lesson->registerFor($presentNovice)->present()->complete();
        $lesson->registerFor($absentNovice)->absent()->complete();

        $resultForPresentNovice = $lesson->isPresent($presentNovice);
        $resultForAbsentNovice = $lesson->isPresent($absentNovice);

        $this->assertTrue($resultForPresentNovice);
        $this->assertFalse($resultForAbsentNovice);
    }

    /** @test */
    public function can_check_the_absence_of_a_novice()
    {
        $lesson = Lesson::factory()->hasNovices(2)->create();
        $absentNovice = $lesson->novices->first();
        $presentNovice = $lesson->novices->last();
        $lesson->registerFor($absentNovice)->absent()->complete();
        $lesson->registerFor($presentNovice)->present()->complete();

        $resultForAbsentNovice = $lesson->isAbsent($absentNovice);
        $resultForPresentNovice = $lesson->isAbsent($presentNovice);

        $this->assertTrue($resultForAbsentNovice);
        $this->assertFalse($resultForPresentNovice);
    }

    /** @test */
    public function the_presence_of_enrolled_novice_but_with_no_presence_registered_should_be_null()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $lesson->enroll($novice);

        $this->assertNull($novice->lessons->first()->presence->present);
    }

    /** @test */
    public function can_update_a_novices_presence_from_present_to_absent()
    {
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();
        $lesson->registerFor($novice)->present()->complete();
        $this->assertTrue($lesson->isPresent($novice));

        $lesson->registerFor($novice)->absent()->complete();

        $this->assertTrue($lesson->isAbsent($novice));
    }

    /** @test */
    public function can_update_a_novices_presence_from_absent_to_present()
    {
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();
        $lesson->registerFor($novice)->absent()->complete();
        $this->assertTrue($lesson->isAbsent($novice));

        $lesson->registerFor($novice)->present()->complete();

        $this->assertTrue($lesson->isPresent($novice));
    }

    /** @test */
    public function can_update_a_novices_presence_with_same_actual_value()
    {
        $lesson = Lesson::factory()->hasNovices(2)->create();
        $presentNovice = $lesson->novices->first();
        $absentNovice = $lesson->novices->last();
        $lesson->registerFor($presentNovice)->present()->complete();
        $lesson->registerFor($absentNovice)->absent()->complete();
        $this->assertTrue($lesson->isPresent($presentNovice));
        $this->assertTrue($lesson->isAbsent($absentNovice));

        $lesson->registerFor($presentNovice)->present()->complete();
        $lesson->registerFor($absentNovice)->absent()->complete();

        $this->assertTrue($lesson->isPresent($presentNovice));
        $this->assertTrue($lesson->isAbsent($absentNovice));
    }

    /** @test */
    public function can_get_formatted_json_for_novices_with_no_presence_registered()
    {
        $lesson = Lesson::factory()->hasNovices(5)->create();
        $novicesIds = $lesson->novices->pluck('id');
        $expectedResult = $lesson->novices->reduce(function ($expectedResult, $novice) {
            $expectedResult[$novice->id] = [
                'presence'      => 1,
                'observation'   => null,
            ];
            return $expectedResult;
        }, []);

        $result = $lesson->novicesPresenceToJson(); 

        $this->assertEquals(json_encode($expectedResult), $result);
    }

    /** @test */
    public function can_get_formatted_json_for_novices_with_different_presences_registered()
    {
        $lesson = Lesson::factory()->hasNovices(5)->create();
        $novices = $lesson->novices;
        $novices->each(function ($novice, $key) use ($lesson) {
            if ($key % 2 == 0) {
                $lesson->registerFor($novice)->present()->complete();
            } else {
                $lesson->registerFor($novice)->absent()->complete();
            }
        });
        $expectedResult = $novices->reduce(function ($expectedResult, $novice) use ($lesson) {
            $expectedResult[$novice->id] = [ 
                'presence' => $novice->presentForLesson($lesson) ? 1 : 0,
                'observation' => null,
            ];
            return $expectedResult;
        }, []);

        $result = $lesson->novicesPresenceToJson(); 

        $this->assertEquals(json_encode($expectedResult), $result);
    }

    /** @test */
    public function can_check_an_employer_has_novices_enrolled_to_a_lesson()
    {
        $lessonForEmployer = Lesson::factory()->hasNovices(1)->create();
        $lessonNotForEmployer = Lesson::factory()->hasNovices(1)->create();
        $noviceForEmployer = $lessonForEmployer->novices()->first();
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])->create();
        $employer->novices()->save($noviceForEmployer);

        $lessonForEmployerResult = $lessonForEmployer->hasNovicesForEmployer($employer);
        $lessonNotForEmployerResult = $lessonNotForEmployer->hasNovicesForEmployer($employer);

        $this->assertTrue($lessonForEmployerResult);
        $this->assertFalse($lessonNotForEmployerResult);
    }

    /** @test */
    public function get_lessons_for_today()
    {
        $todayLessons = Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->count(3)->create();
        $yesterdayLessons = Lesson::factory()->notRegistered()->hasNovices(3)->count(3)->create(['date' => Carbon::parse('yesterday')]);
        $tomorrowLessons = Lesson::factory()->notRegistered()->hasNovices(3)->count(3)->create(['date' => Carbon::parse('tomorrow')]);

        $result = Lesson::today()->get();

        $this->assertEquals($todayLessons->pluck('id'), $result->pluck('id'));
        $this->assertNotEquals($yesterdayLessons->pluck('id'), $result->pluck('id'));
        $this->assertNotEquals($tomorrowLessons->pluck('id'), $result->pluck('id'));
    }

    /** @test */
    public function get_lessons_for_week()
    {
        $weekLessons = Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->count(3)->create();
        $lastWeekLessons = Lesson::factory()->notRegistered()->hasNovices(3)->count(3)->create(['date' => Carbon::parse('-1 week')]);
        $nextWeekLessons = Lesson::factory()->notRegistered()->hasNovices(3)->count(3)->create(['date' => Carbon::parse('+1 week')]);

        $result = Lesson::week()->get();

        $this->assertEquals($weekLessons->pluck('id'), $result->pluck('id'));
        $this->assertNotEquals($lastWeekLessons->pluck('id'), $result->pluck('id'));
        $this->assertNotEquals($nextWeekLessons->pluck('id'), $result->pluck('id'));
    }

    /** @test */
    public function register_an_observation_for_a_novice()
    {
        $lesson = Lesson::factory()->notRegistered()->hasNovices(1)->create();
        $novice = $lesson->novices->first();

        $lesson->registerFor($novice)->present()->observation('test observation for a novice')->complete();

        $this->assertEquals('test observation for a novice', $lesson->fresh()->novices->find($novice)->presence->observation);
    }

    /** @test */
    public function register_an_observation_for_a_novice_and_leaving_it_null_for_another()
    {
        $lesson = Lesson::factory()->notRegistered()->hasNovices(2)->create();
        $noviceA = $lesson->novices->first();
        $noviceB = $lesson->novices->last();

        $lesson->registerFor($noviceA)->present()->observation('test observation for a novice')->complete();
        $lesson->registerFor($noviceB)->present()->complete();

        $this->assertEquals('test observation for a novice', $lesson->fresh()->novices->find($noviceA)->presence->observation);
        $this->assertNull($lesson->fresh()->novices->find($noviceB)->presence->observation);
    }

    /** @test */
    public function updating_an_observation()
    {
        $lesson = Lesson::factory()->notRegistered()->hasNovices(1)->create();
        $novice = $lesson->novices->first();
        $lesson->registerFor($novice)->present()->observation('test observation for a novice')->complete();

        $lesson->registerFor($novice)->present()->observation('update test observation for a novice')->complete();

        $this->assertEquals('update test observation for a novice', $lesson->fresh()->novices->find($novice)->presence->observation);
    }

    /** @test */
    public function trying_to_update_the_register_of_a_registered_lesson_should_throw_an_exception()
    {
        $lesson = Lesson::factory()->notRegistered()->hasNovices(1)->create();
        $novice = $lesson->novices->first();
        $lesson->registerFor($novice)->present()->observation('test observation for a novice')->complete();
        $lesson->register();

        try {
            $lesson->fresh()
                   ->registerFor($novice)
                   ->absent()
                   ->observation('update observation for a novice')
                   ->complete();
        } catch (LessonRegisteredException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to update a register to a lesson already registered should throw an exception');
    }

    /** @test */
    public function can_update_the_register_of_a_registered_lesson_when_there_is_a_peding_rectification_request()
    {
        $lesson = Lesson::factory()->notRegistered()->hasNovices(1)->hasRequests(1)->create();
        $novice = $lesson->novices->first();
        $lesson->registerFor($novice)->present()->observation('test observation for a novice')->complete();
        $lesson->register = 'original register';
        $lesson->register();
        $lesson->openRequest()->release();
        $originalRegisterDate = $lesson->registered_at;

        $this->travel(5)->minutes();
        $lesson->registerFor($novice)->absent()->observation('update observation for novice')->complete();
        $lesson->register = 'updated register';
        $lesson->register();

        $lesson->refresh();
        $this->assertTrue($lesson->isAbsent($novice));
        $this->assertEquals('update observation for novice', $lesson->observationFor($novice));
        $this->assertEquals('updated register', $lesson->register);
        $this->assertEquals($originalRegisterDate, $lesson->registered_at);
    }

    /** @test */
    public function get_an_observation_registered_for_a_novice()
    {
        $lesson = Lesson::factory()->notRegistered()->hasNovices(1)->create();
        $novice = $lesson->novices->first();
        $lesson->registerFor($novice)->present()->observation('test observation for a novice')->complete();

        $result = $lesson->observationFor($novice);

        $this->assertEquals('test observation for a novice', $result);
    }

    /** @test */
    public function get_an_instructor_lessons()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lessons = Lesson::factory()->instructor($instructor)->count(3)->create();
        
        $result = Lesson::forInstructor($instructor)->get();

        $this->assertEquals($lessons->pluck('id')->toArray(), $result->pluck('id')->toArray());
    }

    /** @test */
    public function get_lessons_for_novices_that_belongs_to_an_employer()
    {
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])->create();
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();
        $employer->novices()->save($novice);
        $lessons = Lesson::factory()->count(3)->create();
        $lessons->each(function ($lesson) use ($novice) {
            $lesson->enroll($novice);
        });
        
        $result = Lesson::forEmployer($employer)->get();

        $this->assertEquals($lessons->pluck('id')->toArray(), $result->pluck('id')->toArray());
    }

    /** @test */
    public function get_a_novice_lessons()
    {
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();
        $lessons = Lesson::factory()->count(3)->create();
        $lessons->each(function ($lesson) use ($novice) {
            $lesson->enroll($novice);
        });
        
        $result = Lesson::forNovice($novice)->get();

        $this->assertEquals($lessons->pluck('id')->toArray(), $result->pluck('id')->toArray());
    }

    /** @test */
    public function get_related_course_classes()
    {
        $classA = CourseClass::factory()->create(['name' => 'janeiro - 2020']);
        $classB = CourseClass::factory()->create(['name' => 'julho - 2020']);
        $classC = CourseClass::factory()->create(['name' => 'janeiro - 2021']);
        $novicesForClassA = User::factory()->hasRoles(1,['name' => 'novice'])->count(3)->create();
        $novicesForClassB = User::factory()->hasRoles(1,['name' => 'novice'])->count(3)->create();
        $lesson = Lesson::factory()->notRegistered()->create();
        $novicesForClassA->each(function ($novice) use ($classA, $lesson) {
            $classA->subscribe($novice);
            $lesson->enroll($novice);
        });
        $novicesForClassB->each(function ($novice) use ($classB, $lesson) {
            $classB->subscribe($novice);
            $lesson->enroll($novice);
        });

        $result = $lesson->relatedCourseClasses();

        $this->assertEquals(['janeiro - 2020', 'julho - 2020'], $result);
    }
    
    /** @test */
    public function get_formatted_related_course_classes()
    {
        $classA = CourseClass::factory()->create(['name' => 'janeiro - 2020']);
        $classB = CourseClass::factory()->create(['name' => 'julho - 2020']);
        $classC = CourseClass::factory()->create(['name' => 'janeiro - 2021']);
        $novicesForClassA = User::factory()->hasRoles(1,['name' => 'novice'])->count(3)->create();
        $novicesForClassB = User::factory()->hasRoles(1,['name' => 'novice'])->count(3)->create();
        $lesson = Lesson::factory()->notRegistered()->create();
        $novicesForClassA->each(function ($novice) use ($classA, $lesson) {
            $classA->subscribe($novice);
            $lesson->enroll($novice);
        });
        $novicesForClassB->each(function ($novice) use ($classB, $lesson) {
            $classB->subscribe($novice);
            $lesson->enroll($novice);
        });

        $result = $lesson->formatted_course_classes;

        $this->assertEquals('janeiro - 2020 | julho - 2020', $result);
    }
}
