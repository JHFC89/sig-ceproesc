<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\{User, Company, Invitation, Registration};
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListEmployerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $coordinator;

    protected $registrations;

    private $company;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::fakeCoordinator();

        $this->company = Company::factory()->create();

        $this->registrations = Registration::factory()->count(3)
                                                      ->forEmployer($this->company->id)
                                                      ->create();

        $this->registrations->each(function ($registration) {
            $registration->invitation()->save(new Invitation([
                'email' => $this->faker->unique()->safeEmail,
                'code' => 'TESTCODE1234',
            ]));
        });

    }

    /** @test */
    public function coordinator_can_view_a_list_of_employers()
    {
        $response = $this->actingAs($this->coordinator)
                         ->get(route('companies.employers.index', [
                             'company' => $this->company,
                         ]));

        $response->assertOk()
                 ->assertViewIs('employers.index')
                 ->assertViewHas('company')
                 ->assertViewHas('registrations')
                 ->assertSee($this->registrations[0]->name)
                 ->assertSee($this->registrations[1]->name)
                 ->assertSee($this->registrations[2]->name);
    }

    /** @test */
    public function guest_cannot_view_a_list_of_employers()
    {
        $response = $this->get(route('companies.employers.index', [
            'company' => $this->company,
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_a_list_of_employers()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('companies.employers.index', [
                             'company' => $this->company,
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_a_list_of_employers()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('companies.employers.index', [
                             'company' => $this->company,
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_a_list_of_employers()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('companies.employers.index', [
                             'company' => $this->company,
                         ]));


        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_a_list_of_employers()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('companies.employers.index', [
                             'company' => $this->company,
                         ]));


        $response->assertUnauthorized();
    }
}
