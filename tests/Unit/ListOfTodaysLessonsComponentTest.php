<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\View\Components\Lesson\ForTodayList;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListOfTodaysLessonsComponentTest extends TestCase
{
    use RefreshDatabase;

    protected $instructor;

    protected $novice;

    protected function setUp():void
    {
        parent::setUp();

        $this->instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $this->novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();
    }

    protected function forTodayListComponent($title, $user)
    {
        return new ForTodayList($title, $user);
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
        Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->instructor($this->instructor)
            ->create([
                'class' => '2020 - janeiro',
                'discipline' => 'fake discipline',
            ]);
        Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->instructor($this->instructor)
            ->create([
                'class' => '2020 - julho',
                'discipline' => 'fakest discipline',
            ]);

        $component = $this->component(ForTodayList::class, ['title' => 'Today', 'user' => $this->instructor]);

        $component
            ->assertSee('Today')
            ->assertSee(today()->format('d/m/Y'))
            ->assertSee('fake discipline')
            ->assertSee('2020 - janeiro')
            ->assertSee('fakest discipline')
            ->assertSee('2020 - julho');
    }

    /** @test */
    public function instructor_cannot_see_lessons_he_is_not_assigned_to()
    {
        $instructorA = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $instructorB = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lessonForInstructorA = Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->create([
                'instructor_id' => $instructorA,
                'discipline' => 'instructor A discipline',
            ]);
        $lessonForInstructorB = Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->create([
                'instructor_id' => $instructorB,
                'discipline' => 'instructor B discipline',
            ]);
        
        $component = $this->component(ForTodayList::class, ['user' => $instructorA]);

        $component
            ->assertSee('instructor A discipline')
            ->assertDontSee('instructor B discipline');
    }

    /** @test */
    public function novice_can_see_lessons_he_is_enrolled_to()
    {
        $lesson = Lesson::factory()->forToday()->notRegistered()->instructor($this->instructor)->create();
        $lesson->enroll($this->novice);

        $component = $this->forTodayListComponent('Today', $this->novice);

        $this->assertComponentHasLesson($component, $lesson);
    }

    /** @test */
    public function novice_cannot_see_lessons_he_is_not_enrolled_to()
    {
        $lessonForAnotherNovice = Lesson::factory()->forToday()->notRegistered()->hasNovices(1)->instructor($this->instructor)->create();

        $component = $this->forTodayListComponent('Today', $this->novice);

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

        $component->assertDontSee(route('lessons.register.create', ['lesson' => $lesson]));
    }

    /** @test */
    public function users_cannot_see_lessons_for_another_day()
    {
        $lessonForAnotherDay = Lesson::factory()->notForToday()->notRegistered()->hasNovices(3)->instructor($this->instructor)->create(); 
        $lessonForAnotherDay->enroll($this->novice);

        $componentForInstructor = $this->forTodayListComponent('Today', $this->instructor);
        $componentForNovice = $this->forTodayListComponent('Today', $this->novice);

        $this->assertComponentDoesNotHaveLesson($componentForInstructor, $lessonForAnotherDay);
        $this->assertComponentDoesNotHaveLesson($componentForNovice, $lessonForAnotherDay);
    }

    /** @test */
    public function show_no_lessons_message_if_there_is_no_lesson_available()
    {
        $component = $this->component(ForTodayList::class, ['user' => $this->instructor]);

        $component->assertSee('Nenhuma aula para hoje');
    }
}
