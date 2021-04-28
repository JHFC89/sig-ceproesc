<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCoordinatorTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp():void
    {
        parent::setUp();

        $this->admin = User::fakeAdmin();
    }

    /** @test */
    public function admin_can_create_an_coordinator()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('coordinators.create'));

        $response->assertOk()
                 ->assertViewIs('coordinators.create');
    }

    /** @test */
    public function guest_cannot_create_an_coordinator()
    {
        $response = $this->get(route('coordinators.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_create_an_coordinator()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('coordinators.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function coordinator_cannot_create_an_coordinator()
    {
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)
                         ->get(route('coordinators.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_create_an_coordinator()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('coordinators.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_create_an_coordinator()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('coordinators.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_create_an_coordinator()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('coordinators.create'));

        $response->assertUnauthorized();
    }
}
