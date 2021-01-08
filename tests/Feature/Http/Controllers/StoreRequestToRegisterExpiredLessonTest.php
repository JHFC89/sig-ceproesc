<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\LessonRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreRequestToRegisterExpiredLessonTest extends TestCase
{
    use RefreshDatabase;

    private $lesson;

    private $instructor;

    private $data;

    protected function setUp():void
    {
        parent::setUp();

        $this->lesson = Lesson::factory()->expired()->hasNovices(3)->create();
        $this->lesson->setTestData();

        $this->instructor = $this->lesson->instructor;

        $this->data = ['justification' => 'Test Request Justification'];
    }

    /** @test */
    public function a_request_to_register_an_expired_lesson_can_be_created()
    {
        $this->withoutExceptionHandling();
        $data = ['justification' => 'Test Request Justification'];

        $response = $this->actingAs($this->instructor)->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $data);

        $response
            ->assertOk()
            ->assertViewIs('requests.show');
        $this->assertEquals(1, LessonRequest::count());
        $this->assertEquals($this->lesson->id, LessonRequest::first()->lesson->id);
        $this->assertEquals('Test Request Justification', LessonRequest::first()->justification);
        $this->assertFalse(LessonRequest::first()->isRectification());
    }

    /** @test */
    public function guest_cannot_create_a_request()
    {
        $response = $this->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertRedirect(route('login'));
        $this->assertEquals(0, LessonRequest::count());
    }

    /** @test */
    public function only_an_instructor_can_create_a_request()
    {
        $notAnInstructor = User::factory()->create();

        $response = $this->actingAs($notAnInstructor)->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(0, LessonRequest::count());
    }

    /** @test */
    public function only_the_lessons_instructor_can_create_a_request()
    {
        $instructorForAnotherLesson = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $response = $this->actingAs($instructorForAnotherLesson)->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(0, LessonRequest::count());
    }

    /** @test */
    public function cannot_create_a_request_for_a_lesson_that_is_not_expired()
    {
        $lessonNotExpired = Lesson::factory()->forToday()->instructor($this->instructor)->create();

        $response = $this->actingAs($this->instructor)->post(route('lessons.requests.store', ['lesson' => $lessonNotExpired]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(0, LessonRequest::count());
    }

    /** @test */
    public function cannot_create_a_request_for_a_lesson_that_has_an_open_request()
    {
        $this->lesson->requests()->create(['justification' => 'Open Request Justification']);

        $response = $this->actingAs($this->instructor)->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(1, LessonRequest::count());
        $this->assertEquals('Open Request Justification', LessonRequest::first()->justification);
    }

    /** @test */
    public function cannot_create_a_request_for_a_lesson_that_has_a_pending_request()
    {
        $request = $this->lesson->requests()->create(['justification' => 'Open Request Justification']);
        $request->release();

        $response = $this->actingAs($this->instructor)->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function justification_is_required()
    {
        $data = [];

        $response = $this->actingAs($this->instructor)->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $data);

        $response->assertSessionHasErrors(['justification']);
    }

    /** @test */
    public function justification_must_be_a_string()
    {
        $data = ['justification' => 1];

        $response = $this->actingAs($this->instructor)->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $data);

        $response->assertSessionHasErrors(['justification']);
    }
}
