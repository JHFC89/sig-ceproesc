<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\RegisterLessonRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequestPermissionToRegisterExpiredLessonTest extends TestCase
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
    public function an_instructor_can_view_the_request_permission_to_register_expired_lesson_page()
    {
        $response = $this->actingAs($this->lesson->instructor)->get(route('lessons.requests.create', ['lesson' => $this->lesson]));

        $response
            ->assertOk()
            ->assertViewIs('lessons.requests.create')
            ->assertSee($this->lesson->instructor->name)
            ->assertSee($this->lesson->formatted_date)
            ->assertSee($this->lesson->formatted_course_classes)
            ->assertSee($this->lesson->discipline)
            ->assertSee($this->lesson->hourly_load)
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

        $response->assertNotFound();
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

        $response->assertNotFound();
    }

    /** @test */
    public function cannot_view_the_request_page_for_a_lesson_that_has_an_open_request()
    {
        $this->lesson->requests()->create(['justification' => 'test justification']);

        $response = $this->actingAs($this->instructor)->get(route('lessons.requests.create', ['lesson' => $this->lesson]));
        
        $response->assertNotFound();
    }

    /** @test */
    public function an_request_to_register_an_expired_lesson_can_be_created()
    {
        $data = ['justification' => 'Test Request Justification'];

        $response = $this->actingAs($this->instructor)->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $data);

        $response
            ->assertOk()
            ->assertViewIs('requests.show');
        $this->assertEquals(1, RegisterLessonRequest::count());
        $this->assertEquals($this->lesson->id, RegisterLessonRequest::first()->lesson->id);
        $this->assertEquals('Test Request Justification', RegisterLessonRequest::first()->justification);
    }

    /** @test */
    public function guest_cannot_create_a_request()
    {
        $response = $this->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertRedirect(route('login'));
        $this->assertEquals(0, RegisterLessonRequest::count());
    }

    /** @test */
    public function only_an_instructor_can_create_a_request()
    {
        $notAnInstructor = User::factory()->create();

        $response = $this->actingAs($notAnInstructor)->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(0, RegisterLessonRequest::count());
    }

    /** @test */
    public function only_the_lessons_instructor_can_create_a_request()
    {
        $instructorForAnotherLesson = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $response = $this->actingAs($instructorForAnotherLesson)->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(0, RegisterLessonRequest::count());
    }

    /** @test */
    public function cannot_create_a_request_for_a_lesson_that_has_an_open_request()
    {
        $this->lesson->requests()->create(['justification' => 'Open Request Justification']);

        $response = $this->actingAs($this->instructor)->post(route('lessons.requests.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(1, RegisterLessonRequest::count());
        $this->assertEquals('Open Request Justification', RegisterLessonRequest::first()->justification);
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
