<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Registration;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyAdminCoordinatorTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $adminCoordinator;

    protected $registration;

    protected $from;

    protected function setUp():void
    {
        parent::setUp();

        $this->admin = User::fakeAdmin();

        $this->adminCoordinator = $this->adminCoordinator();

        $this->registration = $this->adminCoordinator->registration;

        $this->from = route('admins.show', [
            'registration' => $this->adminCoordinator->registration,
        ]);
    }

    private function adminCoordinator()
    {
        $registration = Registration::factory()->forCoordinator()->create();
        $registration->phones()->create(['number' => '123456789']);

        $invitation = $registration->invitation()
                                   ->save(Invitation::factory()
                                   ->create());

        $adminCoordinator = User::factory()->create([
            'email' => $invitation->email
        ]);

        $registration->attachUser($adminCoordinator);

        $adminCoordinator->roles()
                         ->attach(Role::firstOrCreate(['name' => Role::ADMIN]));

        return $adminCoordinator;
    }

    /** @test */
    public function admin_can_destroy_a_registered_admin_coordinator()
    {
        $adminCoordinator = $this->adminCoordinator();
        $this->assertTrue($adminCoordinator->isCoordinator());
        $this->assertTrue($adminCoordinator->isAdmin());

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $adminCoordinator->registration->id
                         ]));

        $response->assertOk()
                 ->assertViewHas('registration')
                 ->assertViewIs('coordinators.show')
                 ->assertSessionHas('status', 'Coordenador não é mais Administrador.');

        $adminCoordinator->refresh();
        $this->assertTrue($adminCoordinator->isCoordinator());
        $this->assertFalse($adminCoordinator->isAdmin());
    }

    /** @test */
    public function admin_can_destroy_an_unregistered_admin_coordinator()
    {
        $registration = Registration::factory()->forAdmin()->create();
        $registration->invitation()->save(Invitation::factory()->create());
        $registration->phones()->create(['number' => '123456789']);
        $this->assertTrue($registration->isForAdmin());
        $this->assertFalse($registration->isForCoordinator());

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $registration->id
                         ]));

        $response->assertOk()
                 ->assertViewHas('registration')
                 ->assertViewIs('coordinators.show')
                 ->assertSessionHas('status', 'Coordenador não é mais Administrador.');

        $registration->refresh();
        $this->assertTrue($registration->isForCoordinator());
        $this->assertFalse($registration->isForAdmin());
    }

    /** @test */
    public function cannot_destroy_a_registered_coordinator()
    {
        $registration = Registration::factory()->forCoordinator()->create();
        $invitation = $registration->invitation()
                                   ->save(Invitation::factory()
                                   ->create());
        $coordinator = User::factory()->create(['email' => $invitation->email]);
        $registration->attachUser($coordinator);
        $this->assertTrue($coordinator->isCoordinator());
        $this->assertFalse($coordinator->isAdmin());

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $registration->id
                         ]));

        $response->assertNotFound();

        $coordinator->refresh();
        $this->assertTrue($coordinator->isCoordinator());
        $this->assertFalse($coordinator->isAdmin());
    }

    /** @test */
    public function cannot_destroy_a_registered_instructor()
    {
        $registration = Registration::factory()->forInstructor()->create();
        $invitation = $registration->invitation()
                                   ->save(Invitation::factory()
                                   ->create());
        $instructor = User::factory()->create(['email' => $invitation->email]);
        $registration->attachUser($instructor);
        $this->assertTrue($instructor->isInstructor());
        $this->assertFalse($instructor->isCoordinator());
        $this->assertFalse($instructor->isAdmin());

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $registration->id
                         ]));

        $response->assertNotFound();

        $instructor->refresh();
        $this->assertTrue($instructor->isInstructor());
        $this->assertFalse($instructor->isCoordinator());
        $this->assertFalse($instructor->isAdmin());
    }

    /** @test */
    public function cannot_destroy_a_registered_novice()
    {
        $registration = Registration::factory()->forNovice()->create();
        $invitation = $registration->invitation()
                                   ->save(Invitation::factory()
                                   ->create());
        $novice = User::factory()->create(['email' => $invitation->email]);
        $registration->attachUser($novice);
        $this->assertTrue($novice->isNovice());
        $this->assertFalse($novice->isCoordinator());
        $this->assertFalse($novice->isAdmin());

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $registration->id
                         ]));

        $response->assertNotFound();

        $novice->refresh();
        $this->assertTrue($novice->isNovice());
        $this->assertFalse($novice->isCoordinator());
        $this->assertFalse($novice->isAdmin());
    }

    /** @test */
    public function cannot_destroy_a_registered_employer()
    {
        $registration = Registration::factory()->forEmployer()->create();
        $invitation = $registration->invitation()
                                   ->save(Invitation::factory()
                                   ->create());
        $employer = User::factory()->create(['email' => $invitation->email]);
        $registration->attachUser($employer);
        $this->assertTrue($employer->isEmployer());
        $this->assertFalse($employer->isCoordinator());
        $this->assertFalse($employer->isAdmin());

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $registration->id
                         ]));

        $response->assertNotFound();

        $employer->refresh();
        $this->assertTrue($employer->isEmployer());
        $this->assertFalse($employer->isCoordinator());
        $this->assertFalse($employer->isAdmin());
    }

    /** @test */
    public function cannot_destroy_an_unregistered_coordinator()
    {
        $registration = Registration::factory()->forCoordinator()->create();
        $registration->invitation()->save(Invitation::factory()->create());
        $this->assertTrue($registration->isForCoordinator());
        $this->assertFalse($registration->isForAdmin());

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $registration->id
                         ]));

        $response->assertNotFound();

        $registration->refresh();
        $this->assertTrue($registration->isForCoordinator());
        $this->assertFalse($registration->isForAdmin());
    }

    /** @test */
    public function cannot_destroy_an_unregistered_instructor()
    {
        $registration = Registration::factory()->forInstructor()->create();
        $registration->invitation()->save(Invitation::factory()->create());
        $this->assertTrue($registration->isForInstructor());
        $this->assertFalse($registration->isForAdmin());

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $registration->id
                         ]));

        $response->assertNotFound();

        $registration->refresh();
        $this->assertTrue($registration->isForInstructor());
        $this->assertFalse($registration->isForAdmin());
    }

    /** @test */
    public function cannot_destroy_an_unregistered_novice()
    {
        $registration = Registration::factory()->forNovice()->create();
        $registration->invitation()->save(Invitation::factory()->create());
        $this->assertTrue($registration->isForNovice());
        $this->assertFalse($registration->isForAdmin());

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $registration->id
                         ]));

        $response->assertNotFound();

        $registration->refresh();
        $this->assertTrue($registration->isForNovice());
        $this->assertFalse($registration->isForAdmin());
    }

    /** @test */
    public function cannot_destroy_an_unregistered_employer()
    {
        $registration = Registration::factory()->forEmployer()->create();
        $registration->invitation()->save(Invitation::factory()->create());
        $this->assertTrue($registration->isForEmployer());
        $this->assertFalse($registration->isForAdmin());

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $registration->id
                         ]));

        $response->assertNotFound();

        $registration->refresh();
        $this->assertTrue($registration->isForEmployer());
        $this->assertFalse($registration->isForAdmin());
    }

    /** @test */
    public function guest_cannot_destroy_an_admin_coordinador()
    {
        $response = $this->delete(route('admin-coordinators.destroy', [
            'registration' => $this->registration->id
        ]));

        $response->assertRedirect('login');
        $this->assertTrue($this->adminCoordinator->isAdmin());
    }

    /** @test */
    public function user_without_role_cannot_destroy_an_admin_coordinador()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $this->registration->id
                         ]));

        $response->assertUnauthorized();
        $this->assertTrue($this->adminCoordinator->isAdmin());
    }

    /** @test */
    public function coordinator_cannot_destroy_an_admin_coordinador()
    {
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $this->registration->id
                         ]));

        $response->assertUnauthorized();
        $this->assertTrue($this->adminCoordinator->isAdmin());
    }

    /** @test */
    public function instructor_cannot_destroy_an_admin_coordinador()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $this->registration->id
                         ]));

        $response->assertUnauthorized();
        $this->assertTrue($this->adminCoordinator->isAdmin());
    }

    /** @test */
    public function novice_cannot_destroy_an_admin_coordinador()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $this->registration->id
                         ]));

        $response->assertUnauthorized();
        $this->assertTrue($this->adminCoordinator->isAdmin());
    }

    /** @test */
    public function employer_cannot_destroy_an_admin_coordinador()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->delete(route('admin-coordinators.destroy', [
                             'registration' => $this->registration->id
                         ]));

        $response->assertUnauthorized();
        $this->assertTrue($this->adminCoordinator->isAdmin());
    }
}
