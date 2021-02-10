<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Couse;
use App\Models\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;

class CreateCourseTest extends TestCase
{
    use RefreshDatabase;

    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::factory()
             ->hasRoles(1, ['name' => 'coordinator'])
             ->create();
    }

    /** @test */
    public function can_view_the_page_to_create_a_course()
    {
        $response = $this
            ->actingAs($this->coordinator)
            ->get(route('courses.create'));

        $response
            ->assertOk()
            ->assertViewIs('courses.create')
            ->assertViewHas('basic_disciplines')
            ->assertViewHas('specific_disciplines')
            ->assertSee(route('courses.store'));
    }

    /** @test */
    public function guest_cannot_create_a_course()
    {
        $response = $this->get(route('courses.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_create_a_course()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('courses.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_create_a_course()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this->actingAs($instructor)->get(route('courses.create'));

        $response->assertUnauthorized();
    }
    //novice
    /** @test */
    public function novice_cannot_create_a_course()
    {
        $novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();

        $response = $this->actingAs($novice)->get(route('courses.create'));

        $response->assertUnauthorized();
    }
    //employer
    /** @test */
    public function employer_cannot_create_a_course()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this->actingAs($employer)->get(route('courses.create'));

        $response->assertUnauthorized();
    }
}
