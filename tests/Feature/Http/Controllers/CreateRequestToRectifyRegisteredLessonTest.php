<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateRequestToRectifyRegisteredLessonTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp():void
    {
        parent::setUp();

        $this->lesson = Lesson::factory()->registered()->hasNovices(3)->create();
        $this->lesson->setTestData();

        $this->instructor = $this->lesson->instructor;

        $this->data = ['justification' => 'Test Request Justification'];
    }

    /** @test */
    public function instructor_can_view_the_request_to_rectify_lesson_page()
    {
        $response = $this->actingAs($this->instructor)->get(route('lessons.requests.create', ['lesson' => $this->lesson]));

        $response
            ->assertOk()
            ->assertViewIs('lessons.requests.create')
            ->assertSee($this->lesson->instructor->name)
            ->assertSee($this->lesson->formatted_date)
            ->assertSee($this->lesson->formatted_course_classes)
            ->assertSee($this->lesson->discipline->name)
            ->assertDontSee($this->lesson->discipline)
            ->assertSee($this->lesson->hourly_load)
            ->assertSee('solicitação de retificação de aula registrada')
            ->assertSee('Digite aqui a justificativa para retificar a aula')
            ->assertSee(route('lessons.requests.store', ['lesson' => $this->lesson]));
    }

    /** @test */
    public function guest_cannot_view_the_request_to_rectify_lesson_page()
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
    public function cannot_view_the_request_page_for_a_lesson_that_is_not_registered()
    {
        $lessonNotRegistered = Lesson::factory()->notRegistered()->create();
        
        $response = $this->actingAs($lessonNotRegistered->instructor)->get(route('lessons.requests.create', ['lesson' => $lessonNotRegistered]));

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
