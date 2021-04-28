<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\{User, Invitation, Registration};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewAdminTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $registration;

    protected function setUp():void
    {
        parent::setUp();

        $this->admin = User::fakeAdmin();

        $this->registration = Registration::factory()->forAdmin()
                                                     ->create();

        $this->registration->invitation()->save(new Invitation([
            'email' => 'fakeadmin@test.com',
            'code' => 'TESTCODE1234',
        ]));
    }

    /** @test */
    public function admin_can_view_an_admin_registration()
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->admin)
                         ->get(route('admins.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertOk()
                 ->assertViewIs('admins.show')
                 ->assertViewHas('registration')
                 ->assertSee($this->registration->name)
                 ->assertSee($this->registration->email);
    }

    /** @test */
    public function cannot_view_a_registration_for_coordinator()
    {
        $registration = Registration::factory()->forCoordinator()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admins.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function cannot_view_a_registration_for_instructor()
    {
        $registration = Registration::factory()->forInstructor()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admins.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function cannot_view_a_registration_for_novice()
    {
        $registration = Registration::factory()->forNovice()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admins.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function cannot_view_a_registration_for_employer()
    {
        $registration = Registration::factory()->forEmployer()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admins.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function guest_cannot_view_an_admin()
    {
        $response = $this->get(route('admins.show', [
            'registration' => $this->registration
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_an_admin()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('admins.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function coordinator_cannot_view_an_admin()
    {
        $coordinator = User::fakeInstructor();

        $response = $this->actingAs($coordinator)
                         ->get(route('admins.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_an_admin()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('admins.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_an_admin()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('admins.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_an_admin()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('admins.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }
}
