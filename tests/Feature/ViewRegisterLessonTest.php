<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewRegisterLessonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_a_lesson_available_to_registration_at_current_date()
    {
        $lesson = Lesson::factory()->forToday()->hasNovices(3)->create();
        extract($lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');

        $reponse = $this->get('lessons/register/create/' . $lesson->id);

        $reponse
            ->assertOk()
            ->assertViewHas('lesson', $lesson)
            ->assertSee($lesson->class)
            ->assertSee($lesson->discipline)
            ->assertSee($lesson->hourly_load)
            ->assertSee($novice_0->name)
            ->assertSee($novice_1->name)
            ->assertSee($novice_2->name);
    }

    /** @test */
    public function a_user_cannot_view_a_lesson_already_registered_even_if_its_available_for_current_date()
    {
        $lesson = Lesson::factory()->forToday()->registered()->create([]);

        $response = $this->get('lessons/register/create/' . $lesson->id);

        $response->assertStatus(404);
    }

    /** @test */
    public function a_user_can_view_the_register_draft()
    {
        $lesson = Lesson::factory()->forToday()->draft()->create([]);

        $reponse = $this->get('lessons/register/create/' . $lesson->id);

        $reponse
            ->assertOk()
            ->assertSee($lesson->register);
    }
}
