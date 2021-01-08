<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateRequestToRegisterExpiredLessonTest extends TestCase
{
    use RefreshDatabase;

    private $lesson;

    private $instructor;

    protected function setUp():void
    {
        parent::setUp();

        $this->lesson = Lesson::factory()->expired()->hasNovices(3)->create();
        $this->lesson->setTestData();

        $this->instructor = $this->lesson->instructor;
    }

    /** @test */
    public function instructor_can_view_the_page_to_request_to_register_an_expired_lesson()
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->lesson->instructor)->get(route('lessons.requests.create', ['lesson' => $this->lesson]));

        $response
            ->assertOk()
            ->assertViewIs('lessons.requests.create')
            ->assertSee($this->lesson->instructor->name)
            ->assertSee($this->lesson->formatted_date)
            ->assertSee($this->lesson->formatted_course_classes)
            ->assertSee($this->lesson->discipline)
            ->assertSee($this->lesson->hourly_load)
            ->assertSee('solicitação de liberação de aula vencida')
            ->assertSee('Digite aqui a justificativa do atraso para registrar a aula')
            ->assertSee(route('lessons.requests.store', ['lesson' => $this->lesson]));
    }

    /** @test */
    public function guest_cannot_view_the_request_permission_to_register_expired_lesson_page()
    {
        $response = $this->get(route('lessons.requests.create', ['lesson' => $this->lesson]));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function only_an_instructor_can_view_the_request_page()
    {
        $notAnInstructor = User::factory()->create();

        $response = $this->actingAs($notAnInstructor)->get(route('lessons.requests.create', ['lesson' => $this->lesson]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function only_the_lessons_instructor_can_view_the_request_page()
    {
        $instructorForAnotherLesson = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $response = $this->actingAs($instructorForAnotherLesson)->get(route('lessons.requests.create', ['lesson' => $this->lesson]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function cannot_view_the_request_page_for_a_lesson_that_is_not_expired()
    {
        $lessonNotExpired = Lesson::factory()->forToday()->create();
        
        $response = $this->actingAs($lessonNotExpired->instructor)->get(route('lessons.requests.create', ['lesson' => $lessonNotExpired]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function cannot_view_the_request_page_for_a_lesson_that_has_an_open_request()
    {
        $this->lesson->requests()->create(['justification' => 'test justification']);

        $response = $this->actingAs($this->instructor)->get(route('lessons.requests.create', ['lesson' => $this->lesson]));
        
        $response->assertUnauthorized();
    }

    /** @test */
    public function cannot_view_the_request_page_for_a_lesson_that_has_a_pending_request()
    {
        $request = $this->lesson->requests()->create(['justification' => 'test justification']);
        $request->release();

        $response = $this->actingAs($this->instructor)->get(route('lessons.requests.create', ['lesson' => $this->lesson]));
        
        $response->assertUnauthorized();
    }
}
