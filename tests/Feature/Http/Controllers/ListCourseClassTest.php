<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CourseClass;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListCourseClassTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_view_a_list_of_classes()
    {
        $courseClasses = CourseClass::factory()->count(2)->create();
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)->get(route('classes.index'));

        $response
            ->assertOk()
            ->assertViewIs('classes.index')
            ->assertViewHas('courseClasses')
            ->assertSee($courseClasses[0]->name)
            ->assertSee($courseClasses[1]->name);
    }

    /** @test */
    public function guest_cannot_see_a_list_of_classes()
    {
        $response = $this->get(route('classes.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_see_a_list_of_courses()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('classes.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_can_see_a_list_of_courses()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)->get(route('classes.index'));

        $response->assertOk();
    }

    /** @test */
    public function novice_cannot_see_a_list_of_courses()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)->get(route('classes.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_see_a_list_of_courses()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)->get(route('classes.index'));

        $response->assertUnauthorized();
    }
}
