<?php

namespace Tests\Unit;

use App\Models\Discipline;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\CourseClass;
use App\View\Components\Lesson\ForTodayList;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListOfTodaysLessonsComponentTest extends TestCase
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
    }

    protected function forTodayListComponent($user, $title = 'Today')
    {
        return new ForTodayList($user, $title);
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
    public function instructor_can_see_lessons_he_is_assigned_to()
    {
        $courseClassA = CourseClass::factory()->create([
            'name' => 'janeiro - 2020'
        ]);
        $disciplineA = Discipline::factory()->create([
            'name' => 'fake discipline'
        ]);
        $lessonA = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->instructor($this->instructor)
            ->create([
                'discipline_id' => $disciplineA,
            ]);
        $lessonA->novices->each(function ($novice) use ($courseClassA) {
            $novice->turnIntoNovice();
            $courseClassA->subscribe($novice);
        });
        $courseClassB = CourseClass::factory()->create([
            'name' => 'julho - 2020'
        ]);
        $disciplineB = Discipline::factory()->create([
            'name' => 'fakest discipline'
        ]);
        $lessonB = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->instructor($this->instructor)
            ->create([
                'discipline_id' => $disciplineB,
            ]);
        $lessonB->novices->each(function ($novice) use ($courseClassB) {
            $novice->turnIntoNovice();
            $courseClassB->subscribe($novice);
        });

        $component = $this->component(ForTodayList::class, ['title' => 'Today', 'user' => $this->instructor]);

        $component
            ->assertSee('Today')
            ->assertSee(today()->format('d/m/Y'))
            ->assertSee('fake discipline')
            ->assertSee('janeiro - 2020')
            ->assertSee('fakest discipline')
            ->assertSee('julho - 2020');
    }

    /** @test */
    public function instructor_cannot_see_lessons_he_is_not_assigned_to()
    {
        $instructorA = User::fakeInstructor();
        $instructorB = User::fakeInstructor();
        $disciplineA = Discipline::factory()->create([
            'name' => 'instructor A discipline'
        ]);
        $disciplineB = Discipline::factory()->create([
            'name' => 'instructor B discipline'
        ]);
        $lessonForInstructorA = Lesson::factory()->forToday()
                                                 ->notRegistered()
                                                 ->hasNovices(3)
                                                 ->create([
                                                    'instructor_id' => $instructorA,
                                                    'discipline_id' => $disciplineA,
                                                ]);
        $lessonForInstructorB = Lesson::factory()->forToday()
                                                 ->notRegistered()
                                                 ->hasNovices(3)
                                                 ->create([
                                                    'instructor_id' => $instructorB,
                                                    'discipline_id' => $disciplineB,
                                                ]);
        
        $component = $this->component(ForTodayList::class, ['user' => $instructorA]);

        $component
            ->assertSee('instructor A discipline')
            ->assertDontSee('instructor B discipline');
    }

    /** @test */
    public function instructor_can_see_link_to_register_class()
    {
        $lesson = Lesson::factory()->forToday()->notRegistered()->instructor($this->instructor)->create();

        $component = $this->component(ForTodayList::class, ['user' => $this->instructor]);

        $component->assertSee(route('lessons.registers.create', ['lesson' => $lesson]));
    }

    /** @test */
    public function novice_can_see_lessons_he_is_enrolled_to()
    {
        $lesson = Lesson::factory()->forToday()->notRegistered()->instructor($this->instructor)->create();
        $lesson->enroll($this->novice);

        $component = $this->forTodayListComponent($this->novice);

        $this->assertComponentHasLesson($component, $lesson);
    }

    /** @test */
    public function novice_cannot_see_lessons_he_is_not_enrolled_to()
    {
        $lessonForAnotherNovice = Lesson::factory()->forToday()->notRegistered()->hasNovices(1)->instructor($this->instructor)->create();

        $component = $this->forTodayListComponent($this->novice);

        $this->assertComponentDoesNotHaveLesson($component, $lessonForAnotherNovice);
    }

    /** @test */
    public function novice_can_see_lessons_instructor()
    {
        $lesson = Lesson::factory()->forToday()->notRegistered()->instructor($this->instructor)->create();
        $lesson->enroll($this->novice);

        $component = $this->component(ForTodayList::class, ['user' => $this->novice]);

        $component->assertSee($this->instructor->name);
    }

    /** @test */
    public function novice_cannot_see_link_to_register_class()
    {
        $lesson = Lesson::factory()->forToday()->notRegistered()->instructor($this->instructor)->create();
        $lesson->enroll($this->novice);

        $component = $this->component(ForTodayList::class, ['user' => $this->novice]);

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
        $lesson = Lesson::factory()->forToday()->notRegistered()->instructor($this->instructor)->create();
        $lesson->enroll($noviceA);
        $lesson->enroll($noviceB);
        
        $componentForNoviceA = $this->component(ForTodayList::class, ['user' => $noviceA]);
        $componentForNoviceB = $this->component(ForTodayList::class, ['user' => $noviceB]);

        $componentForNoviceA
            ->assertSee('class A')
            ->assertDontSee('class B');
        $componentForNoviceB
            ->assertSee('class B')
            ->assertDontSee('class A');
    }

    /** @test */
    public function employer_can_see_their_novices_classes()
    {
        $noviceA = User::fakeNovice();
        $noviceB = User::fakeNovice();
        $this->employer->company->novices()->saveMany([
            $noviceA->registration,
            $noviceB->registration,
        ]);
        $lesson = Lesson::factory()->forToday()->notRegistered()->instructor($this->instructor)->create();
        $lesson->enroll($noviceA);
        $lesson->enroll($noviceB);
        
        $component = $this->forTodayListComponent($this->employer);

        $this->assertComponentHasLesson($component, $lesson);
    }

    /** @test */
    public function employer_cannot_see_another_employer_novices_classes()
    {
        $noviceA = User::fakeNovice();
        $employerA = User::fakeEmployer();
        $employerA->company->novices()->save($noviceA->registration);
        $lessonForNoviceA = Lesson::factory()->forToday()
                                             ->notRegistered()
                                             ->instructor($this->instructor)
                                             ->create();
        $lessonForNoviceA->enroll($noviceA);
        $noviceB = User::fakeNovice();
        $employerB = User::fakeEmployer();
        $employerB->company->novices()->save($noviceB->registration);
        $lessonForNoviceB = Lesson::factory()->forToday()
                                             ->notRegistered()
                                             ->instructor($this->instructor)
                                             ->create();
        $lessonForNoviceB->enroll($noviceB);
        
        $componentForEmployerA = $this->forTodayListComponent($employerA);
        $componentForEmployerB = $this->forTodayListComponent($employerB);

        $this->assertComponentHasLesson($componentForEmployerA, $lessonForNoviceA);
        $this->assertComponentDoesNotHaveLesson($componentForEmployerA, $lessonForNoviceB);
        $this->assertComponentHasLesson($componentForEmployerB, $lessonForNoviceB);
        $this->assertComponentDoesNotHaveLesson($componentForEmployerB, $lessonForNoviceA);
    }

    /** @test */
    public function employer_cannot_see_link_to_register_class()
    {
        $this->employer->company->novices()->save($this->novice->registration);
        $lesson = Lesson::factory()->forToday()
                                   ->notRegistered()
                                   ->instructor($this->instructor)
                                   ->create();
        $lesson->enroll($this->novice);

        $component = $this->component(ForTodayList::class, [
            'user' => $this->employer
        ]);

        $component->assertDontSee(route('lessons.registers.create', [
            'lesson' => $lesson
        ]));
    }

    /** @test */
    public function users_cannot_see_lessons_for_another_day()
    {
        $this->employer->company->novices()->save($this->novice->registration);
        $lessonForAnotherDay = Lesson::factory()->notForToday()
                                                ->notRegistered()
                                                ->hasNovices(3)
                                                ->instructor($this->instructor)
                                                ->create(); 
        $lessonForAnotherDay->enroll($this->novice);

        $componentForInstructor = $this->forTodayListComponent($this->instructor);
        $componentForNovice = $this->forTodayListComponent($this->novice);
        $componentForEmployer = $this->forTodayListComponent($this->employer);

        $this->assertComponentDoesNotHaveLesson($componentForInstructor, $lessonForAnotherDay);
        $this->assertComponentDoesNotHaveLesson($componentForNovice, $lessonForAnotherDay);
        $this->assertComponentDoesNotHaveLesson($componentForEmployer, $lessonForAnotherDay);
    }

    /** @test */
    public function show_no_lessons_message_if_there_is_no_lesson_available()
    {
        $component = $this->component(ForTodayList::class, ['user' => $this->instructor]);

        $component->assertSee('Nenhuma aula para hoje');
    }

    /** @test */
    public function can_hide_registered_field()
    {
        Lesson::factory()->forToday()->hasNovices(3)->instructor($this->instructor)->create();
        
        $component = $this->component(ForTodayList::class, [
            'user' => $this->instructor,
            'hideRegistered' => true,
        ]);

        $component->assertDontSee('registrada');
    }
}
