<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCourseClassTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function coordinator_can_view_the_create_course_class_page()
    {
        $coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create();
            
        $response = $this->actingAs($coordinator)->get(route('classes.create'));

        $response
            ->assertOk()
            ->assertViewIs('classes.create');
    }

    /** @test */
    public function guest_cannot_view_the_create_course_class_page()
    {
        $response = $this->get(route('classes.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_roles_cannot_view_the_create_course_class_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('classes.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_the_create_course_class_page()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this->actingAs($instructor)->get(route('classes.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_the_create_course_class_page()
    {
        $novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();

        $response = $this->actingAs($novice)->get(route('classes.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_the_create_course_class_page()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this->actingAs($employer)->get(route('classes.create'));

        $response->assertUnauthorized();
    }
}
