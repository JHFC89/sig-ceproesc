<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Discipline;
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
        $this->withoutExceptionHandling();
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
        $instructorA = User::fakeInstructor();
        $instructorB = User::fakeInstructor();
        $disciplineA = Discipline::factory()->create([
            'name' => 'instructor A discipline'
        ]);
        $disciplineB = Discipline::factory()->create([
            'name' => 'instructor B discipline'
        ]);
        $lessonForInstructorA = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructorA,
                'discipline_id' => $disciplineA,
            ]);
        $lessonForInstructorB = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructorB,
                'discipline_id' => $disciplineB,
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
        $instructorA = User::fakeInstructor();
        $instructorB = User::fakeInstructor();
        $disciplineA = Discipline::factory()->create([
            'name' => 'instructor A discipline'
        ]);
        $disciplineB = Discipline::factory()->create([
            'name' => 'instructor B discipline'
        ]);
        $lessonForInstructorA = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructorA,
                'discipline_id' => $disciplineA,
            ]);
        $lessonForInstructorB = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructorB,
                'discipline_id' => $disciplineB,
            ]);

        $response = $this->actingAs($instructorA)->get('lessons/week');

        $response
            ->assertOk()
            ->assertSee('instructor A discipline')
            ->assertDontSee('instructor B discipline');
    }
}
