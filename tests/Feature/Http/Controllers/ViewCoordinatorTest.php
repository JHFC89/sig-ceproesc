<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\{User, Invitation, Registration};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewCoordinatorTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $registration;

    protected function setUp():void
    {
        parent::setUp();

        $this->admin = User::fakeAdmin();

        $this->registration = Registration::factory()->forCoordinator()
                                                     ->create();

        $this->registration->invitation()->save(new Invitation([
            'email' => 'fakecoordinator@test.com',
            'code' => 'TESTCODE1234',
        ]));

        $this->registration->phones()->create(['number' => '123456789']);
    }

    /** @test */
    public function admin_can_view_an_coordinator_registration()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('coordinators.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertOk()
                 ->assertViewIs('coordinators.show')
                 ->assertViewHas('registration')
                 ->assertSee($this->registration->name)
                 ->assertSee($this->registration->email)
                 ->assertSee($this->registration->phones[0]->number);
    }

    /** @test */
    public function cannot_view_a_registration_for_instructor()
    {
        $registration = Registration::factory()->forInstructor()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('coordinators.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function cannot_view_a_registration_for_novice()
    {
        $registration = Registration::factory()->forNovice()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('coordinators.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function cannot_view_a_registration_for_employer()
    {
        $registration = Registration::factory()->forEmployer()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('coordinators.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function guest_cannot_view_an_coordinator()
    {
        $response = $this->get(route('coordinators.show', [
            'registration' => $this->registration
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_an_coordinator()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('coordinators.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function coordinator_cannot_view_an_coordinator()
    {
        $coordinator = User::fakeInstructor();

        $response = $this->actingAs($coordinator)
                         ->get(route('coordinators.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_an_coordinator()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('coordinators.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_an_coordinator()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('coordinators.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_an_coordinator()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('coordinators.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }
}
