<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\CourseClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewCourseClassTest extends TestCase
{
    use RefreshDatabase;

    protected $courseClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->courseClass = CourseClass::factory()->forCourse()->create();
    }


    /** @test */
    public function coordinator_can_view_a_course_class()
    {
        $coordinator = User::factory()
            ->hasRoles(['name' => 'coordinator'])
            ->create();
        $courseClass = CourseClass::factory()->forCourse()->create();

        $response = $this
            ->actingAs($coordinator)
            ->get(route('classes.show', ['courseClass' => $courseClass]));

        $response
            ->assertOk()
            ->assertViewIs('classes.show')
            ->assertViewHas('courseClass');
    }

    /** @test */
    public function guest_cannot_view_a_course_class()
    {
        $response = $this->get(route('classes.show', [
            'courseClass' => $this->courseClass
        ]));

        $response->assertRedirect('login');
    }
    
    /** @test */
    public function user_without_role_cannot_view_a_course_class()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('classes.show', [
            'courseClass' => $this->courseClass
        ]));

        $response->assertUnauthorized();
    }
    
    /** @test */
    public function instructor_cannot_view_a_course_class()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this->actingAs($instructor)->get(route('classes.show', [
            'courseClass' => $this->courseClass
        ]));

        $response->assertUnauthorized();
    }
    
    /** @test */
    public function novice_cannot_view_a_course_class()
    {
        $novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();

        $response = $this->actingAs($novice)->get(route('classes.show', [
            'courseClass' => $this->courseClass
        ]));

        $response->assertUnauthorized();
    }
    
    /** @test */
    public function employer_cannot_view_a_course_class()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this->actingAs($employer)->get(route('classes.show', [
            'courseClass' => $this->courseClass
        ]));

        $response->assertUnauthorized();
    }
}
