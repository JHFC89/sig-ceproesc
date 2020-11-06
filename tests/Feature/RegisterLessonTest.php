<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterLessonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_be_registered()
    {
        $lesson = Lesson::factory()->forToday()->create();
        
        $response = $this->postJson('api/lessons/register/' . $lesson->id, [
            'register' => 'Example lesson register',
        ]);

        $response->assertStatus(201);
        $this->assertEquals('Example lesson register', $lesson->fresh()->register);
    }

    /** @test */
    public function a_registered_lesson_cannot_be_registered_again()
    {
        $lesson = Lesson::factory()->registered()->create();
        
        $response = $this->postJson('api/lessons/register/' . $lesson->id, [
            'register' => 'Trying to register lesson again',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'error' => 'Lesson already registered',
            ]);
        $this->assertNotEquals('Trying to register lesson again', $lesson->fresh()->register);
    }

    /** @test */
    public function only_todays_lesson_can_be_registered()
    {
        $lesson = Lesson::factory()->notForToday()->create();

        $response = $this->postJson('api/lessons/register/' . $lesson->id, [
            'register' => 'Trying to register unavailable date lesson',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'error' => 'Lesson is not available to register at this date',
            ]);
        $this->assertNotEquals('Trying to register unavailable date lesson', $lesson->fresh()->register);
    }

    /** @test */
    public function register_field_is_required()
    {
        $lesson = Lesson::factory()->forToday()->create();
        
        $response = $this->postJson('api/lessons/register/' . $lesson->id, []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['register']);
    }

    /** @test */
    public function a_user_can_view_a_lesson_available_to_registration_at_current_date()
    {
        $lesson = Lesson::factory()->forToday()->create([]);

        $reponse = $this->get('lessons/register/create/' . $lesson->id);

        $reponse
            ->assertOk()
            ->assertViewHas('lesson', $lesson)
            ->assertSee($lesson->class)
            ->assertSee($lesson->discipline)
            ->assertSee($lesson->hourly_load)
            ->assertSee($lesson->novice);
    }

    /** @test */
    public function a_user_cannot_view_a_lesson_already_registered_even_if_its_available_for_current_date()
    {
        $lesson = Lesson::factory()->forToday()->registered()->create([]);

        $response = $this->get('lessons/register/create/' . $lesson->id);

        $response->assertStatus(404);
    }
}
