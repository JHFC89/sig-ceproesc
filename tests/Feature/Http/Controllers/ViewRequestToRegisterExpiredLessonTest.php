<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\RegisterLessonRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewRequestToRegisterExpiredLessonTest extends TestCase
{
    use RefreshDatabase;

    private $lesson;

    private $instructor;

    private $request;

    protected function setUp():void
    {
        parent::setUp();

        $this->lesson = Lesson::factory()->expired()->hasNovices(3)->create();
        $this->lesson->setTestData();

        $this->request = RegisterLessonRequest::for($this->lesson, 'Test Justification'); 

        $this->instructor = $this->lesson->instructor;
    }

    /** @test */
    public function view_a_request_to_register_an_expired_lesson()
    {
        $response = $this->actingAs($this->instructor)->get(route('requests.show', ['request' => $this->request]));

        $response
            ->assertOk()
            ->assertViewIs('requests.show')
            ->assertSee($this->request->id)
            ->assertSee($this->request->created_at->format('d/m/Y'))
            ->assertSee($this->request->lesson->instructor->name)
            ->assertSee($this->request->justification)
            ->assertSee($this->request->lesson->formatted_date)
            ->assertSee($this->request->lesson->relatedCourseClasses());
    }

    /** @test */
    public function guest_cannot_view_a_request()
    {
        $response = $this->get(route('requests.show', ['request' => $this->request]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_that_is_not_an_instructor_cannot_view_the_request()
    {
        $response = $this->actingAs(User::factory()->create())->get(route('requests.show', ['request' => $this->request]));

        $response->assertNotFound();
    }

    /** @test */
    public function instructor_that_is_not_the_creator_cannot_view_the_request()
    {
        $response = $this->actingAs(User::factory()->hasRoles(1, ['name' => 'instructor'])->create())->get(route('requests.show', ['request' => $this->request]));

        $response->assertUnauthorized();
    }
}
