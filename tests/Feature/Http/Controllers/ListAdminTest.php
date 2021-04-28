<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\{User, Invitation, Registration};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListAdminTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected $registrations;

    protected function setUp():void
    {
        parent::setUp();

        $this->admin = User::fakeAdmin();

        $this->registrations = Registration::factory()->count(3)
                                                      ->forAdmin()
                                                      ->create();

        Registration::whereAdmin()->get()->each(function ($registration) {
            $registration->invitation()->save(new Invitation([
                'email' => $this->faker->unique()->safeEmail,
                'code' => 'TESTCODE1234',
            ]));
        });
    }

    /** @test */
    public function admin_can_view_a_list_of_admins()
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->admin)
                         ->get(route('admins.index'));

        $response->assertOk()
                 ->assertViewIs('admins.index')
                 ->assertViewHas('registrations')
                 ->assertSee($this->registrations[0]->name)
                 ->assertSee($this->registrations[1]->name)
                 ->assertSee($this->registrations[2]->name);
    }

    /** @test */
    public function cannot_view_registrations_that_are_not_for_admins()
    {
        $coordinator = Registration::factory()->forCoordinator()->create();
        $instructor = Registration::factory()->forInstructor()->create();
        $novice = Registration::factory()->forNovice()->create();
        $employer = Registration::factory()->forEmployer()->create();
        
        $response = $this->actingAs($this->admin)
                         ->get(route('admins.index'));

        $response->assertOk()
                 ->assertDontSee($coordinator->name)
                 ->assertDontSee($instructor->name)
                 ->assertDontSee($novice->name)
                 ->assertDontSee($employer->name);
    }

    /** @test */
    public function guest_cannot_view_a_list_of_admins()
    {
        $response = $this->get(route('admins.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_a_list_of_admins()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admins.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function coordinator_cannot_view_a_list_of_admins()
    {
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)->get(route('admins.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_a_list_of_admins()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)->get(route('admins.index'));


        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_a_list_of_admins()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)->get(route('admins.index'));


        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_a_list_of_admins()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)->get(route('admins.index'));

        $response->assertUnauthorized();
    }
}
