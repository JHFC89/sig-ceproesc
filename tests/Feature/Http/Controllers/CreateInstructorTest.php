<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateInstructorTest extends TestCase
{
    use RefreshDatabase;

    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::fakeCoordinator();
    }

    /** @test */
    public function coordinator_can_create_an_instructor()
    {
        $response = $this->actingAs($this->coordinator)
                         ->get(route('instructors.create'));

        $response->assertOk()
                 ->assertViewIs('instructors.create');
    }

    /** @test */
    public function guest_cannot_create_an_instructor()
    {
        $response = $this->get(route('instructors.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_create_an_instructor()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('instructors.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_create_an_instructor()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('instructors.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_create_an_instructor()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)->get(route('instructors.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_create_an_instructor()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('instructors.create'));

        $response->assertUnauthorized();
    }
}
