<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewRegisterLessonTest extends TestCase
{
    use RefreshDatabase;

    private $instructor;
    private $lesson;

    protected function setUp():void
    {
        parent::setUp();

        $this->lesson = Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->create();
        $this->instructor = $this->lesson->instructor;
    }

    /** @test */
    public function an_instructor_can_view_a_lesson_available_to_registration_at_current_date()
    {
        extract($this->lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');

        $response = $this->actingAs($this->instructor)->get('lessons/register/create/' . $this->lesson->id);

        $response
            ->assertOk()
            ->assertViewHas('lesson', $this->lesson)
            ->assertSee($this->lesson->class)
            ->assertSee($this->lesson->discipline)
            ->assertSee($this->lesson->hourly_load)
            ->assertSee($novice_0->name)
            ->assertSee($novice_1->name)
            ->assertSee($novice_2->name);
    }

    /** @test */
    public function a_guest_cannot_view_the_registation_page()
    {
        $response = $this->get('lessons/register/create/' . $this->lesson->id);

        $response
            ->assertStatus(302)
            ->assertRedirect('/login');
    }

    /** @test */
    public function only_an_instructor_can_view_the_registration_page()
    {
        $notInstructor = User::factory()->create();

        $response = $this->actingAs($notInstructor)->get('lessons/register/create/' . $this->lesson->id);

        $response->assertUnauthorized();
    }

    /** @test */
    public function an_instructor_cannot_view_the_registration_page_for_a_lesson_he_is_not_assigned_to()
    {
        $lessonForAnotherInstructor = Lesson::factory()->forToday()->hasNovices(3)->create();
        $this->assertNotEquals($lessonForAnotherInstructor->instructor->id, $this->instructor->id);

        $response = $this->actingAs($this->instructor)->get('lessons/register/create/' . $lessonForAnotherInstructor->id);

        $response->assertUnauthorized();
    }

    /** @test */
    public function an_instructor_cannot_view_a_lesson_not_available_to_registration_at_current_date()
    {
        $lesson = Lesson::factory()->notForToday()->notRegistered()->create([]);

        $response = $this->actingAs($lesson->instructor)->get('lessons/register/create/' . $lesson->id);

        $response->assertNotFound();
    }

    /** @test */
    public function an_instructor_cannot_view_a_lesson_already_registered_even_if_its_available_at_current_date()
    {
        $lesson = Lesson::factory()->forToday()->registered()->create([]);

        $response = $this->actingAs($lesson->instructor)->get('lessons/register/create/' . $lesson->id);

        $response->assertNotFound();
    }

    /** @test */
    public function an_instructor_can_view_the_register_draft()
    {
        $lesson = Lesson::factory()->draft()->forToday()->create([]);

        $response = $this->actingAs($lesson->instructor)->get('lessons/register/create/' . $lesson->id);

        $response
            ->assertOk()
            ->assertSee($lesson->register);
    }
}
