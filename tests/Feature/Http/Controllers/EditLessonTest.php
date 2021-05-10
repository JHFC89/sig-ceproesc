<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Lesson;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditLessonTest extends TestCase
{
    use RefreshDatabase;

    protected $coordinator;

    protected $lesson;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::fakeCoordinator();

        $this->lesson = Lesson::factory()->create();
    }

    /** @test */
    public function coordinator_can_edit_a_lesson()
    {
        $lesson = Lesson::factory()->create();

        $response = $this->actingAs($this->coordinator)
                         ->get(route('lessons.edit', ['lesson' => $lesson]));

        $response->assertOk()
                 ->assertViewIs('lessons.edit')
                 ->assertViewHas('lesson');
    }

    /** @test */
    public function cannot_edit_a_registered_lesson()
    {
        $lesson = Lesson::factory()->registered()->create();

        $response = $this->actingAs($this->coordinator)
                         ->get(route('lessons.edit', ['lesson' => $lesson]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_edit_a_lesson()
    {
        $response = $this->get(route('lessons.edit', [
            'lesson' => $this->lesson
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_edit_a_lesson()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('lessons.edit', [
                             'lesson' => $this->lesson
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_edit_a_lesson()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('lessons.edit', [
                             'lesson' => $this->lesson
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_edit_a_lesson()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('lessons.edit', [
                             'lesson' => $this->lesson
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_edit_a_lesson()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('lessons.edit', [
                             'lesson' => $this->lesson
                         ]));

        $response->assertUnauthorized();
    }
}
