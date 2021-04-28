<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\{User, Invitation, Registration};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListCoordinatorTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected $registrations;

    protected function setUp():void
    {
        parent::setUp();

        $this->admin = User::fakeAdmin();

        $this->registrations = Registration::factory()->count(3)
                                                      ->forCoordinator()
                                                      ->create();

        Registration::whereCoordinator()->get()->each(function ($registration) {
            $registration->invitation()->save(new Invitation([
                'email' => $this->faker->unique()->safeEmail,
                'code' => 'TESTCODE1234',
            ]));
        });
    }

    /** @test */
    public function admin_can_view_a_list_of_coordinators()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('coordinators.index'));

        $response->assertOk()
                 ->assertViewIs('coordinators.index')
                 ->assertViewHas('registrations')
                 ->assertSee($this->registrations[0]->name)
                 ->assertSee($this->registrations[1]->name)
                 ->assertSee($this->registrations[2]->name);
    }

    /** @test */
    public function cannot_view_registrations_that_are_not_for_coordinators()
    {
        $instructor = Registration::factory()->forInstructor()->create();
        $novice = Registration::factory()->forNovice()->create();
        $employer = Registration::factory()->forEmployer()->create();
        
        $response = $this->actingAs($this->admin)
                         ->get(route('coordinators.index'));

        $response->assertOk()
                 ->assertDontSee($instructor->name)
                 ->assertDontSee($novice->name)
                 ->assertDontSee($employer->name);
    }

    /** @test */
    public function guest_cannot_view_a_list_of_coordinators()
    {
        $response = $this->get(route('coordinators.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_a_list_of_coordinators()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('coordinators.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function coordinator_cannot_view_a_list_of_coordinators()
    {
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)->get(route('coordinators.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_a_list_of_coordinators()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)->get(route('coordinators.index'));


        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_a_list_of_coordinators()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)->get(route('coordinators.index'));


        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_a_list_of_coordinators()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)->get(route('coordinators.index'));

        $response->assertUnauthorized();
    }
}
