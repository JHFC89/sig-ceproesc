<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\{User, Invitation, Registration};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewInstructorTest extends TestCase
{
    use RefreshDatabase;

    protected $coordinator;

    protected $registration;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::fakeCoordinator();

        $this->registration = Registration::factory()->forInstructor()->create([
            'name'          => 'Fake Employer',
            'rg'            => '123-123-12',
        ]);

        $this->registration->invitation()->save(new Invitation([
            'email' => 'fakeinstructor@test.com',
            'code' => 'TESTCODE1234',
        ]));

        $this->registration->phones()->create(['number' => '123456789']);

        $this->registration->address()->create([
            'street'    => 'Fake Street',
            'number'    => '123',
            'district'  => 'Fake Garden',
            'city'      => 'Fake City',
            'state'     => 'Fake State',
            'country'   => 'Fake Country',
            'cep'       => '12.123-123',
        ]);
    }

    /** @test */
    public function coordinator_can_view_an_instructor_registration()
    {
        $response = $this->actingAs($this->coordinator)
                         ->get(route('instructors.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertOk()
                 ->assertViewIs('instructors.show')
                 ->assertViewHas('registration')
                 ->assertSee($this->registration->name)
                 ->assertSee($this->registration->email)
                 ->assertSee($this->registration->formatted_birthdate)
                 ->assertSee($this->registration->rg)
                 ->assertSee($this->registration->cpf)
                 ->assertSee($this->registration->ctps)
                 ->assertSee($this->registration->phones[0]->number)
                 ->assertSee($this->registration->address->street)
                 ->assertSee($this->registration->address->number)
                 ->assertSee($this->registration->address->district)
                 ->assertSee($this->registration->address->city)
                 ->assertSee($this->registration->address->state)
                 ->assertSee($this->registration->address->country)
                 ->assertSee($this->registration->address->cep);
    }

    /** @test */
    public function cannot_view_a_registration_for_coordinator()
    {
        $registration = Registration::factory()->forCoordinator()->create();

        $response = $this->actingAs($this->coordinator)
                         ->get(route('instructors.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function cannot_view_a_registration_for_novice()
    {
        $registration = Registration::factory()->forNovice()->create();

        $response = $this->actingAs($this->coordinator)
                         ->get(route('instructors.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function cannot_view_a_registration_for_employer()
    {
        $registration = Registration::factory()->forEmployer()->create();

        $response = $this->actingAs($this->coordinator)
                         ->get(route('instructors.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function guest_cannot_view_an_instructor()
    {
        $response = $this->get(route('instructors.show', [
            'registration' => $this->registration
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_an_instructor()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('instructors.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_an_instructor()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('instructors.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_an_instructor()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('instructors.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_an_instructor()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('instructors.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }
}
