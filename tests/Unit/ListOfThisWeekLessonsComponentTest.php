<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\CourseClass;
use App\Models\LessonRequest;
use App\View\Components\Lesson\ForWeekList;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListOfThisWeekLessonsComponentTest extends TestCase
{
    use RefreshDatabase;

    protected $instructor;

    protected $novice;

    protected $employer;

    protected function setUp():void
    {
        parent::setUp();

        $this->instructor = User::fakeInstructor();
        $this->novice = User::fakeNovice();
        $this->employer = User::fakeEmployer();

        Carbon::setTestNow(Carbon::parse('next wednesday'));
    }

    public static function tearDownAfterClass():void
    {
        Carbon::setTestNow();
    }

    protected function forThisWeekListComponent($title, $user)
    {
        return new ForWeekList($title, $user);
    }

    protected function assertComponentHasLesson($component, $lesson)
    {
        $this->assertTrue($component->lessons->contains($lesson), 'Failed asserting that component has lesson');
    }

    protected function assertComponentDoesNotHaveLesson($component, $lesson)
    {
        $this->assertFalse($component->lessons->contains($lesson), 'Failed asserting that component does not have lesson');
    }

    /** @test */
    public function lessons_are_show_in_ascending_order_relative_to_their_date()
    {
        $this->travelTo(now()->startOfWeek()->addDay(1));
        $thirdLesson = Lesson::factory()->instructor($this->instructor)
                                        ->create([
                                            'date' => Carbon::parse('tomorrow')
                                        ]);
        $firstLesson = Lesson::factory()->instructor($this->instructor)
                                        ->create([
                                            'date' => Carbon::parse('yesterday')
                                        ]);
        $secondLesson = Lesson::factory()->instructor($this->instructor)
                                         ->create([
                                             'date' => Carbon::parse('today')
                                         ]);
        $lessons = collect([$firstLesson, $secondLesson, $thirdLesson]);
        $lessons->each(function ($lesson) {
            $lesson->enroll($this->novice);
        });
        $this->employer->company->novices()->save($this->novice->registration);

        $componentForInstructor = $this->forThisWeekListComponent(
            'Week',
            $this->instructor
        );
        $componentForNovice = $this->forThisWeekListComponent(
            'Week',
            $this->novice
        );
        $componentForEmployer = $this->forThisWeekListComponent(
            'Week',
            $this->employer
        );

        $this->assertEquals(
            $lessons->pluck('id'),
            $componentForInstructor->lessons->pluck('id')
        );
        $this->assertEquals(
            $lessons->pluck('id'),
            $componentForNovice->lessons->pluck('id')
        );
        $this->assertEquals(
            $lessons->pluck('id'),
            $componentForEmployer->lessons->pluck('id')
        );

        $this->travelBack();
    }

    /** @test */
    public function instructor_can_see_lessons_he_is_assigned_to()
    {
        $lessons = Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->instructor($this->instructor)->count(2)->create();

        $component = $this->forThisWeekListComponent('Week', $this->instructor);
        
        $lessons->each(function ($lesson) use ($component){
            $this->assertComponentHasLesson($component, $lesson);
        });
    }

    /** @test */
    public function instructor_cannot_see_lessons_he_is_not_assigned_to()
    {
        $instructorA = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $instructorB = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lessonForInstructorA = Lesson::factory()->instructor($instructorA)->thisWeek()->notRegistered()->hasNovices(3)->create();
        $lessonForInstructorB = Lesson::factory()->instructor($instructorB)->thisWeek()->notRegistered()->hasNovices(3)->create();
        
        $componentForInstructorA = $this->forThisWeekListComponent('Week', $instructorA);

        $this->assertComponentHasLesson($componentForInstructorA, $lessonForInstructorA);
        $this->assertComponentDoesNotHaveLesson($componentForInstructorA, $lessonForInstructorB);
    }

    /** @test */
    public function instructor_can_see_link_to_register_lesson()
    {
        $lesson = Lesson::factory()->thisWeek()->notRegistered()->instructor($this->instructor)->create();

        $component = $this->component(ForWeekList::class, ['user' => $this->instructor]);

        $component->assertSee(route('lessons.registers.create', ['lesson' => $lesson]));
    }

    /** @test */
    public function instructor_cannot_see_link_to_register_classes_that_are_not_for_today()
    {
        $lesson = Lesson::factory()->notForToday()->notRegistered()->instructor($this->instructor)->create();

        $component = $this->component(ForWeekList::class, ['user' => $this->instructor]);

        $component->assertDontSee(route('lessons.registers.create', ['lesson' => $lesson]));
    }

    /** @test */
    public function instructor_will_see_a_warning_when_a_lesson_register_deadline_is_expired()
    {
        $experiredLesson = Lesson::factory()->expired()->instructor($this->instructor)->create();

        $component = $this->component(ForWeekList::class, ['title' => 'Week', 'user' => $this->instructor]);
        
        $component->assertSee('vencida');
    }

    /** @test */
    public function instructor_will_not_see_a_warning_when_a_lesson_register_deadline_is_not_expired()
    {
        $lesson = Lesson::factory()->forToday()->notRegistered()->instructor($this->instructor)->create();

        $component = $this->component(ForWeekList::class, ['title' => 'Week', 'user' => $this->instructor]);
        
        $component->assertDontSee('vencida');
    }

    /** @test */
    public function instructor_will_see_a_warning_when_a_lesson_has_an_open_request_to_register()
    {
        $openRequestLesson = Lesson::factory()->expired()->instructor($this->instructor)->create();
        LessonRequest::for($openRequestLesson, 'Fake Justification');

        $component = $this->component(ForWeekList::class, ['title' => 'Week', 'user' => $this->instructor]);
        
        $component
            ->assertSee('em análise')
            ->assertDontSee('vencida');
    }

    /** @test */
    public function instructor_can_see_a_link_to_request_permission_to_register_a_expired_lesson()
    {
        $experiredLesson = Lesson::factory()->expired()->instructor($this->instructor)->create();

        $component = $this->component(ForWeekList::class, ['title' => 'Week', 'user' => $this->instructor]);
        
        $component->assertSee(route('lessons.requests.create', ['lesson' => $experiredLesson]));
    }

    /** @test */
    public function instructor_cannot_see_a_link_to_request_permission_to_register_a_not_expired_lesson()
    {
        $lesson = Lesson::factory()->forToday()->notRegistered()->instructor($this->instructor)->create();

        $component = $this->component(ForWeekList::class, ['title' => 'Week', 'user' => $this->instructor]);
        
        $component->assertDontSee(route('lessons.requests.create', ['lesson' => $lesson]));
    }

    /** @test */
    public function instructor_cannot_see_a_link_to_request_permission_to_register_a_lesson_with_an_open_request()
    {
        $openRequestLesson = Lesson::factory()->expired()->instructor($this->instructor)->create();
        LessonRequest::for($openRequestLesson, 'Fake Justification');

        $component = $this->component(ForWeekList::class, ['title' => 'Week', 'user' => $this->instructor]);
        
        $component->assertDontSee(route('lessons.requests.create', ['lesson' => $openRequestLesson]));
    }

    /** @test */
    public function instructor_cannot_see_link_to_register_a_lesson_with_an_open_request()
    {
        $openRequestLesson = Lesson::factory()->expired()->instructor($this->instructor)->create();
        LessonRequest::for($openRequestLesson, 'Fake Justification');

        $component = $this->component(ForWeekList::class, ['user' => $this->instructor]);

        $component->assertDontSee(route('lessons.registers.create', ['lesson' => $openRequestLesson]));
    }

    /** @test */
    public function instructor_will_see_a_warning_when_a_lesson_has_a_pending_request_to_register()
    {
        $lesson = Lesson::factory()->expired()->instructor($this->instructor)->hasRequests(1)->create();
        $lesson->openRequest()->release();

        $component = $this->component(ForWeekList::class, ['title' => 'Week', 'user' => $this->instructor]);
        
        $component
            ->assertSee('liberada')
            ->assertDontSee('em análise');
    }

    /** @test */
    public function novice_can_see_lessons_he_is_enrolled_to()
    {
        $lesson = Lesson::factory()->thisWeek()->notRegistered()->instructor($this->instructor)->create();
        $lesson->enroll($this->novice);

        $component = $this->forThisWeekListComponent('Week', $this->novice);

        $this->assertComponentHasLesson($component, $lesson);
    }

    /** @test */
    public function novice_cannot_see_lessons_he_is_not_enrolled_to()
    {
        $lessonForAnotherNovice = Lesson::factory()->thisWeek()->notRegistered()->hasNovices(1)->instructor($this->instructor)->create();

        $component = $this->forThisWeekListComponent('Week', $this->novice);

        $this->assertComponentDoesNotHaveLesson($component, $lessonForAnotherNovice);
    }

    /** @test */
    public function novice_can_see_lessons_instructor()
    {
        $lesson = Lesson::factory()->thisWeek()->notRegistered()->instructor($this->instructor)->create();
        $lesson->enroll($this->novice);

        $component = $this->component(ForWeekList::class, ['user' => $this->novice]);

        $component->assertSee($this->instructor->name);
    }

    /** @test */
    public function novice_cannot_see_link_to_register_class()
    {
        $lesson = Lesson::factory()->thisWeek()->notRegistered()->instructor($this->instructor)->create();
        $lesson->enroll($this->novice);

        $component = $this->component(ForWeekList::class, ['user' => $this->novice]);

        $component->assertDontSee(route('lessons.registers.create', ['lesson' => $lesson]));
    }

    /** @test */
    public function novice_can_see_only_his_course_class()
    {
        $courseClassA = CourseClass::factory()->create(['name' => 'class A']);
        $noviceA = User::factory()->hasRoles(1, ['name' => 'novice'])->create();
        $courseClassA->subscribe($noviceA);
        $courseClassB = CourseClass::factory()->create(['name' => 'class B']);
        $noviceB = User::factory()->hasRoles(1, ['name' => 'novice'])->create();
        $courseClassB->subscribe($noviceB);
        $lesson = Lesson::factory()->thisWeek()->notRegistered()->instructor($this->instructor)->create();
        $lesson->enroll($noviceA);
        $lesson->enroll($noviceB);
        
        $componentForNoviceA = $this->component(ForWeekList::class, ['user' => $noviceA]);
        $componentForNoviceB = $this->component(ForWeekList::class, ['user' => $noviceB]);

        $componentForNoviceA
            ->assertSee('class A')
            ->assertDontSee('class B');
        $componentForNoviceB
            ->assertSee('class B')
            ->assertDontSee('class A');
    }

    /** @test */
    public function novice_cannot_see_link_to_request_to_register_an_expired_lesson()
    {
        $experiredLesson = Lesson::factory()->expired()->instructor($this->instructor)->create();
        $experiredLesson->enroll($this->novice);

        $component = $this->component(ForWeekList::class, ['user' => $this->novice]);

        $component->assertDontSee(route('lessons.requests.create', ['lesson' => $experiredLesson]));
    }

    /** @test */
    public function novice_cannot_see_a_warning_when_a_lesson_has_an_open_request_to_register()
    {
        $openRequestLesson = Lesson::factory()->expired()->instructor($this->instructor)->create();
        $openRequestLesson->enroll($this->novice);
        LessonRequest::for($openRequestLesson, 'Fake Justification');

        $component = $this->component(ForWeekList::class, ['title' => 'Week', 'user' => $this->novice]);
        
        $component->assertDontSee('em análise');
    }

    /** @test */
    public function novice_cannot_see_a_warning_when_a_lesson_has_a_pending_request_to_register()
    {
        $lesson = Lesson::factory()->expired()->instructor($this->instructor)->hasRequests(1)->create();
        $lesson->enroll($this->novice);
        $lesson->openRequest()->release();

        $component = $this->component(ForWeekList::class, ['title' => 'Week', 'user' => $this->novice]);
        
        $component->assertDontSee('liberada');
    }

    /** @test */
    public function employer_can_see_their_novices_classes()
    {
        $noviceA = User::fakeNovice();
        $noviceB = User::fakeNovice();
        $this->employer->company->novices()->saveMany([
            $noviceA->registration,
            $noviceB->registration
        ]);
        $lesson = Lesson::factory()->thisWeek()
                                   ->notRegistered()
                                   ->instructor($this->instructor)
                                   ->create();
        $lesson->enroll($noviceA);
        $lesson->enroll($noviceB);
        
        $component = $this->forThisWeekListComponent('Today', $this->employer);

        $this->assertComponentHasLesson($component, $lesson);
    }

    /** @test */
    public function employer_cannot_see_another_employer_novices_classes()
    {
        $noviceA = User::fakeNovice();
        $employerA = User::fakeEmployer();
        $employerA->company->novices()->save($noviceA->registration);
        $lessonForNoviceA = Lesson::factory()->thisWeek()
                                             ->notRegistered()
                                             ->instructor($this->instructor)
                                             ->create();
        $lessonForNoviceA->enroll($noviceA);
        $noviceB = User::fakeNovice();
        $employerB = User::fakeEmployer();
        $employerB->company->novices()->save($noviceB->registration);
        $lessonForNoviceB = Lesson::factory()->thisWeek()
                                             ->notRegistered()
                                             ->instructor($this->instructor)
                                             ->create();
        $lessonForNoviceB->enroll($noviceB);
        
        $componentForEmployerA = $this->forThisWeekListComponent('Today', $employerA);
        $componentForEmployerB = $this->forThisWeekListComponent('Today', $employerB);

        $this->assertComponentHasLesson($componentForEmployerA, $lessonForNoviceA);
        $this->assertComponentDoesNotHaveLesson($componentForEmployerA, $lessonForNoviceB);
        $this->assertComponentHasLesson($componentForEmployerB, $lessonForNoviceB);
        $this->assertComponentDoesNotHaveLesson($componentForEmployerB, $lessonForNoviceA);
    }

    /** @test */
    public function employer_cannot_see_link_to_register_class()
    {
        $this->employer->company->novices()->save($this->novice->registration);
        $lesson = Lesson::factory()->thisWeek()
                                   ->notRegistered()
                                   ->instructor($this->instructor)
                                   ->create();
        $lesson->enroll($this->novice);

        $component = $this->component(ForWeekList::class, [
            'user' => $this->employer
        ]);

        $component->assertDontSee(route('lessons.registers.create', [
            'lesson' => $lesson
        ]));
    }

    /** @test */
    public function employer_cannot_see_link_to_request_to_register_an_expired_lesson()
    {
        $this->employer->company->novices()->save($this->novice->registration);
        $experiredLesson = Lesson::factory()->expired()
                                            ->instructor($this->instructor)
                                            ->create();
        $experiredLesson->enroll($this->novice);

        $component = $this->component(ForWeekList::class, [
            'user' => $this->employer
        ]);

        $component->assertDontSee(route('lessons.requests.create', [
            'lesson' => $experiredLesson
        ]));
    }

    /** @test */
    public function employer_cannot_see_a_warning_when_a_lesson_has_a_pending_request_to_register()
    {
        $this->employer->company->novices()->save($this->novice->registration);
        $lesson = Lesson::factory()->expired()
                                   ->instructor($this->instructor)
                                   ->hasRequests(1)
                                   ->create();
        $lesson->enroll($this->novice);
        $lesson->openRequest()->release();

        $component = $this->component(ForWeekList::class, [
            'title' => 'Week',
            'user' => $this->employer
        ]);
        
        $component->assertDontSee('liberada');
    }

    /** @test */
    public function users_cannot_see_lessons_for_another_week()
    {
        Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->instructor($this->instructor)->create();
        $lastWeekLesson = Lesson::factory()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'date' => Carbon::parse('-1 week'),
            ]);
        
        $component = $this->forThisWeekListComponent('Week', $this->instructor);
        
        $this->assertComponentDoesNotHaveLesson($component, $lastWeekLesson);
    }

    /** @test */
    public function users_cannot_see_lessons_for_next_week()
    {
        Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->instructor($this->instructor)->create();
        $nextWeekLesson = Lesson::factory()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'date' => Carbon::parse('+1 week'),
            ]);

        $component = $this->forThisWeekListComponent('Week', $this->instructor);

        $this->assertComponentDoesNotHaveLesson($component, $nextWeekLesson);
    }

    /** @test */
    public function show_no_lessons_message_if_there_is_no_lesson_available()
    {
        $component = $this->component(ForWeekList::class, ['user' => $this->instructor]);

        $component->assertSee('Nenhuma aula para esta semana');
    }
}
