<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp():void
    {
        parent::setUp();

        $this->admin = User::fakeAdmin();
    }

    /** @test */
    public function admin_can_create_an_admin()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admins.create'));

        $response->assertOk()
                 ->assertViewIs('admins.create');
    }

    /** @test */
    public function guest_cannot_create_an_admin()
    {
        $response = $this->get(route('admins.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_create_an_admin()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admins.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function coordinator_cannot_create_an_admin()
    {
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)
                         ->get(route('admins.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_create_an_admin()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('admins.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_create_an_admin()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('admins.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_create_an_admin()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('admins.create'));

        $response->assertUnauthorized();
    }
}
