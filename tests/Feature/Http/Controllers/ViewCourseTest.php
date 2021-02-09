<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewCourseTest extends TestCase
{
    use RefreshDatabase;

    protected $course;

    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();

        $this->course = Course::factory()->hasDisciplines(2)->create([
            'name' => 'test course',
            'duration' => 552,
        ]);

        $this->coordinator = User::factory()
             ->hasRoles(1, ['name' => 'coordinator'])
             ->create();
    }

    /** @test */
    public function coordinator_can_view_course()
    {
        $course = Course::factory()->hasDisciplines(2)->create([
            'name' => 'test name course',
            'duration' => 552,
        ]);

        $response = $this
            ->actingAs($this->coordinator)
            ->get(route('courses.show', ['course' => $course]));

        $response
            ->assertOk()
            ->assertViewIs('courses.show')
            ->assertViewHas('course')
            ->assertSee($course->name)
            ->assertSee($course->duration)
            ->assertSee($course->disciplines->first()->name)
            ->assertSee($course->disciplines->last()->name);
    }

    /** @test */
    public function guest_cannot_view_course()
    {
        $response = $this->get(route('courses.show', [
            'course' => $this->course
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_course()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('courses.show', [
            'course' => $this->course
        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_course()
    {
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();

        $response = $this
            ->actingAs($novice)
            ->get(route('courses.show', [
            'course' => $this->course
        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_course()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this
            ->actingAs($instructor)
            ->get(route('courses.show', [
            'course' => $this->course
        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_course()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this
            ->actingAs($employer)
            ->get(route('courses.show', [
            'course' => $this->course
        ]));

        $response->assertUnauthorized();
    }
}
