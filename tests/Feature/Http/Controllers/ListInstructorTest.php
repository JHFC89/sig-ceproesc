<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\{User, Invitation, Registration};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListInstructorTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $coordinator;

    protected $registrations;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::fakeCoordinator();

        $this->registrations = Registration::factory()->count(3)
                                                      ->forInstructor()
                                                      ->create();

        $this->registrations->each(function ($registration) {
            $registration->invitation()->save(new Invitation([
                'email' => $this->faker->unique()->safeEmail,
                'code' => 'TESTCODE1234',
            ]));
        });
    }

    /** @test */
    public function coordinator_can_view_a_list_of_instructors()
    {
        $response = $this->actingAs($this->coordinator)
                         ->get(route('instructors.index'));

        $response->assertOk()
                 ->assertViewIs('instructors.index')
                 ->assertViewHas('registrations')
                 ->assertSee($this->registrations[0]->name)
                 ->assertSee($this->registrations[1]->name)
                 ->assertSee($this->registrations[2]->name);
    }

    /** @test */
    public function cannot_view_registrations_that_are_not_for_instructors()
    {
        $coordinator = Registration::factory()->forCoordinator()->create();
        $novice = Registration::factory()->forNovice()->create();
        $employer = Registration::factory()->forEmployer()->create();
        
        $response = $this->actingAs($this->coordinator)
                         ->get(route('instructors.index'));

        $response->assertOk()
                 ->assertDontSee($coordinator->name)
                 ->assertDontSee($novice->name)
                 ->assertDontSee($employer->name);
    }

    /** @test */
    public function guest_cannot_view_a_list_of_instructors()
    {
        $response = $this->get(route('instructors.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_a_list_of_instructors()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('instructors.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_a_list_of_instructors()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)->get(route('instructors.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_a_list_of_instructors()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)->get(route('instructors.index'));


        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_a_list_of_instructors()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)->get(route('instructors.index'));

        $response->assertUnauthorized();
    }
}
