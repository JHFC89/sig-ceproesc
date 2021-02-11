<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ListCourseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_view_a_list_of_courses()
    {
        $courses = Course::factory()->count(2)->create();
        $coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create();

        $response = $this->actingAs($coordinator)->get(route('courses.index'));

        $response
            ->assertOk()
            ->assertViewIs('courses.index')
            ->assertViewHas('courses')
            ->assertSee($courses[0]->name)
            ->assertSee($courses[1]->name);
    }

    /** @test */
    public function guest_cannot_see_a_list_of_courses()
    {
        $response = $this->get(route('courses.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_see_a_list_of_courses()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('courses.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_see_a_list_of_courses()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this->actingAs($instructor)->get(route('courses.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_see_a_list_of_courses()
    {
        $novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();

        $response = $this->actingAs($novice)->get(route('courses.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_see_a_list_of_courses()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this->actingAs($employer)->get(route('courses.index'));

        $response->assertUnauthorized();
    }
}
