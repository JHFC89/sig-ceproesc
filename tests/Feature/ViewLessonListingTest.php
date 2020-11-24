<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\View\Components\Lesson\ForTodayList;
use App\View\Components\Lesson\ForWeekList;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewLessonListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function showing_to_an_instructor_a_list_of_lessons_to_register_today()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lesson = Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->instructor($instructor)->create();

        $response = $this->actingAs($instructor)->get('lessons/today');
        
        $response
            ->assertOk()
            ->assertSee('Hoje')
            ->assertSee($lesson->class)
            ->assertSee($lesson->discipline);
    }

    /** @test */
    public function guest_cannot_see_list_of_today_lessons_page()
    {
        Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->create();

        $response = $this->get('lessons/today');
        
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_instructor_cannot_see_a_lesson_of_another_instructor_in_the_list_of_today_lessons()
    {
        $instructorA = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $instructorB = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lessonForInstructorA = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructorA,
                'discipline' => 'instructor A discipline',
            ]);
        $lessonForInstructorB = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructorB,
                'discipline' => 'instructor B discipline',
            ]);

        $response = $this->actingAs($instructorA)->get('lessons/today');

        $response
            ->assertOk()
            ->assertSee('instructor A discipline')
            ->assertDontSee('instructor B discipline');
    }

    /** @test */
    public function showing_an_instructor_a_list_of_lessons_to_register_this_week()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        Carbon::setTestNow(Carbon::parse('next tuesday'));
        $mondayLesson = Lesson::factory()->forYesterday()->notRegistered()->hasNovices(3)->instructor($instructor)->create();
        $tuesdayLesson = Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->instructor($instructor)->create();
        $wednesdayLesson = Lesson::factory()->forTomorrow()->notRegistered()->hasNovices(3)->instructor($instructor)->create();

        $response = $this->actingAs($instructor)->get('lessons/week');
        
        $response
            ->assertOk()
            ->assertSee('Esta Semana')
            ->assertSee($mondayLesson->class)
            ->assertSee($mondayLesson->discipline)
            ->assertSee($tuesdayLesson->class)
            ->assertSee($tuesdayLesson->discipline)
            ->assertSee($wednesdayLesson->class)
            ->assertSee($wednesdayLesson->discipline);

        Carbon::setTestNow();
    }

    /** @test */
    public function guest_cannot_see_list_of_week_lessons_page()
    {
        Lesson::factory()->thisWeek()->notRegistered()->hasNovices(3)->create();

        $response = $this->get('lessons/week');
        
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_instructor_cannot_see_a_lesson_of_another_instructor_in_the_list_of_week_lessons()
    {
        $instructorA = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $instructorB = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lessonForInstructorA = Lesson::factory()
            ->thisWeek()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructorA,
                'discipline' => 'instructor A discipline',
            ]);
        $lessonForInstructorB = Lesson::factory()
            ->thisWeek()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructorB,
                'discipline' => 'instructor B discipline',
            ]);

        $response = $this->actingAs($instructorA)->get('lessons/week');

        $response
            ->assertOk()
            ->assertSee('instructor A discipline')
            ->assertDontSee('instructor B discipline');
    }

    /** @test */
    public function list_of_lessons_to_register_today_component()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->instructor($instructor)
            ->create([
                'class' => '2020 - janeiro',
                'discipline' => 'fake discipline',
            ]);
        Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->instructor($instructor)
            ->create([
                'class' => '2020 - julho',
                'discipline' => 'fakest discipline',
            ]);

        $component = $this->component(ForTodayList::class, ['title' => 'Today', 'user' => $instructor]);

        $component
            ->assertSee('Today')
            ->assertSee(today()->format('d/m/Y'))
            ->assertSee('fake discipline')
            ->assertSee('2020 - janeiro')
            ->assertSee('fakest discipline')
            ->assertSee('2020 - julho');
    }

    /** @test */
    public function for_today_list_component_must_not_show_lessons_for_other_day()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->count(3)->create();
        $lessonForAnotherDay = Lesson::factory()->notForToday()->notRegistered()->hasNovices(3)->create(['discipline' => 'not for today discipline']);
        
        $component = $this->component(ForTodayList::class, ['user' => $instructor]);

        $component
            ->assertDontSee($lessonForAnotherDay->formatted_date)
            ->assertDontSee($lessonForAnotherDay->discipline);
    }

    /** @test */
    public function for_today_list_component_must_not_show_another_instructor_lessons()
    {
        $instructorA = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $instructorB = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lessonForInstructorA = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructorA,
                'discipline' => 'instructor A discipline',
            ]);
        $lessonForInstructorB = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructorB,
                'discipline' => 'instructor B discipline',
            ]);
        
        $component = $this->component(ForTodayList::class, ['user' => $instructorA]);

        $component
            ->assertSee('instructor A discipline')
            ->assertDontSee('instructor B discipline');
    }

    /** @test */
    public function for_today_list_component_will_show_message_if_there_is_no_lesson_available()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $component = $this->component(ForTodayList::class, ['user' => $instructor]);

        $component->assertSee('Nenhuma aula para hoje');
    }

    /** @test */
    public function list_of_lesson_to_register_week_component()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lessons = Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->instructor($instructor)->count(2)->create();
        extract($lessons->all(), EXTR_PREFIX_ALL, 'lesson');

        $component = $this->component(ForWeekList::class, ['title' => 'Week', 'user' => $instructor]);
        
        $component
            ->assertSee('Week')
            ->assertSee($lesson_0->formatted_date)
            ->assertSee($lesson_0->class)
            ->assertSee($lesson_0->discipline)
            ->assertSee($lesson_1->formatted_date)
            ->assertSee($lesson_1->class)
            ->assertSee($lesson_1->discipline);
    }

    /** @test */
    public function for_week_list_component_must_not_show_lessons_for_last_week()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->instructor($instructor)->create();
        $lastWeekLesson = Lesson::factory()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'date' => Carbon::parse('-1 week'),
                'discipline' => 'last week discipline',
            ]);
        
        $component = $this->component(ForWeekList::class, ['user' => $instructor]);
        
        $component
            ->assertDontSee($lastWeekLesson->formatted_date)
            ->assertDontSee($lastWeekLesson->discipline);
    }

    /** @test */
    public function for_week_list_component_must_not_show_lessons_for_next_week()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->instructor($instructor)->create();
        $nextWeekLesson = Lesson::factory()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'date' => Carbon::parse('+1 week'),
                'discipline' => 'next week discipline',
            ]);

        $component = $this->component(ForWeekList::class, ['user' => $instructor]);

        $component
            ->assertDontSee($nextWeekLesson->formatted_date)
            ->assertDontSee($nextWeekLesson->discipline);
    }

    /** @test */
    public function for_week_list_component_must_not_show_another_instructor_lessons()
    {
        $instructorA = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $instructorB = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lessonForInstructorA = Lesson::factory()
            ->thisWeek()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructorA,
                'discipline' => 'instructor A discipline',
            ]);
        $lessonForInstructorB = Lesson::factory()
            ->thisWeek()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructorB,
                'discipline' => 'instructor B discipline',
            ]);
        
        $component = $this->component(ForWeekList::class, ['user' => $instructorA]);

        $component
            ->assertSee('instructor A discipline')
            ->assertDontSee('instructor B discipline');
    }

    /** @test */
    public function for_week_list_component_will_show_message_if_there_is_no_lesson_available()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $component = $this->component(ForWeekList::class, ['user' => $instructor]);

        $component->assertSee('Nenhuma aula para esta semana');
    }
}
