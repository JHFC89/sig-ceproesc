<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\{Invitation, Registration, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreAdminCoordinatorTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $coordinator;

    protected $data;

    protected $from;

    protected function setUp():void
    {
        parent::setUp();

        $this->admin = User::fakeAdmin();

        $this->coordinator = $this->coordinator();

        $this->data = [
            'registration_id' => $this->coordinator->registration->id,
        ];

        $this->from = route('coordinators.show', [
            'registration' => $this->coordinator->registration,
        ]);
    }

    private function coordinator()
    {
        $registration = Registration::factory()->forCoordinator()->create();

        $invitation = $registration->invitation()
                                   ->save(Invitation::factory()
                                   ->create());

        $coordinator = User::factory()->create(['email' => $invitation->email]);

        $registration->attachUser($coordinator);

        return $coordinator;
    }

    /** @test */
    public function admin_can_store_a_registered_admin_coordinador()
    {
        $coordinator = $this->coordinator();
        $registration = $coordinator->registration;
        $this->assertTrue($coordinator->isCoordinator());
        $this->assertFalse($coordinator->isAdmin());

        $data = ['registration_id' => $coordinator->registration->id];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-coordinators.store'), $data);

        $response->assertOk()
                 ->assertViewHas('registration')
                 ->assertViewIs('admins.show')
                 ->assertSessionHas('status', 'Administrador cadastrado com sucesso!');

        $coordinator->refresh();
        $this->assertTrue($coordinator->isAdmin());
        $this->assertTrue($coordinator->isCoordinator());
    }

    /** @test */
    public function admin_can_store_an_unregistered_admin_coordinador()
    {
        $registration = Registration::factory()->forCoordinator()->create();
        $registration->invitation()->save(Invitation::factory()->create());

        $this->assertTrue($registration->isForCoordinator());
        $this->assertFalse($registration->isForAdmin());

        $data = ['registration_id' => $registration->id];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-coordinators.store'), $data);

        $response->assertOk()
                 ->assertViewHas('registration')
                 ->assertViewIs('admins.show')
                 ->assertSessionHas('status', 'Administrador cadastrado com sucesso!');

        $this->assertTrue($registration->refresh()->isForAdmin());
    }

    /** @test */
    public function cannot_store_a_registered_admin_as_admin_coordinador()
    {
        $registration = Registration::factory()->forAdmin()->create();
        $invitation = $registration->invitation()
                                   ->save(Invitation::factory()
                                   ->create());
        $admin = User::factory()->create(['email' => $invitation->email]);
        $registration->attachUser($admin);
        $this->assertTrue($admin->isCoordinator());
        $this->assertTrue($admin->isAdmin());

        $data = ['registration_id' => $admin->registration->id];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-coordinators.store'), $data);

        $response->assertNotFound();

        $admin->refresh();
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->isCoordinator());
    }

    /** @test */
    public function cannot_store_a_registered_instructor_as_admin_coordinador()
    {
        $registration = Registration::factory()->forInstructor()->create();
        $invitation = $registration->invitation()
                                   ->save(Invitation::factory()
                                   ->create());
        $instructor = User::factory()->create(['email' => $invitation->email]);
        $registration->attachUser($instructor);
        $this->assertTrue($instructor->isInstructor());
        $this->assertFalse($instructor->isCoordinator());

        $data = ['registration_id' => $instructor->registration->id];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-coordinators.store'), $data);

        $response->assertNotFound();

        $instructor->refresh();
        $this->assertTrue($instructor->isInstructor());
        $this->assertFalse($instructor->isCoordinator());
    }

    /** @test */
    public function cannot_store_a_registered_novice_as_admin_coordinador()
    {
        $registration = Registration::factory()->forNovice()->create();
        $invitation = $registration->invitation()
                                   ->save(Invitation::factory()
                                   ->create());
        $novice = User::factory()->create(['email' => $invitation->email]);
        $registration->attachUser($novice);
        $this->assertTrue($novice->isNovice());
        $this->assertFalse($novice->isCoordinator());

        $data = ['registration_id' => $novice->registration->id];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-coordinators.store'), $data);

        $response->assertNotFound();

        $novice->refresh();
        $this->assertTrue($novice->isNovice());
        $this->assertFalse($novice->isCoordinator());
    }

    /** @test */
    public function cannot_store_a_registered_employer_as_admin_coordinador()
    {
        $registration = Registration::factory()->forEmployer()->create();
        $invitation = $registration->invitation()
                                   ->save(Invitation::factory()
                                   ->create());
        $employer = User::factory()->create(['email' => $invitation->email]);
        $registration->attachUser($employer);
        $this->assertTrue($employer->isEmployer());
        $this->assertFalse($employer->isCoordinator());

        $data = ['registration_id' => $employer->registration->id];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-coordinators.store'), $data);

        $response->assertNotFound();

        $employer->refresh();
        $this->assertTrue($employer->isEmployer());
        $this->assertFalse($employer->isCoordinator());
    }

    /** @test */
    public function cannot_store_an_unregistered_admin_as_admin_coordinador()
    {
        $registration = Registration::factory()->forAdmin()->create();
        $registration->invitation()->save(Invitation::factory()->create());

        $this->assertFalse($registration->isForCoordinator());
        $this->assertTrue($registration->isForAdmin());

        $data = ['registration_id' => $registration->id];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-coordinators.store'), $data);

        $response->assertNotFound();

        $registration->refresh();
        $this->assertFalse($registration->isForCoordinator());
        $this->assertTrue($registration->isForAdmin());
    }

    /** @test */
    public function cannot_store_an_unregistered_instructor_as_admin_coordinador()
    {
        $registration = Registration::factory()->forInstructor()->create();
        $registration->invitation()->save(Invitation::factory()->create());

        $this->assertFalse($registration->isForCoordinator());
        $this->assertTrue($registration->isForInstructor());

        $data = ['registration_id' => $registration->id];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-coordinators.store'), $data);

        $response->assertNotFound();

        $registration->refresh();
        $this->assertFalse($registration->isForCoordinator());
        $this->assertTrue($registration->isForInstructor());
    }

    /** @test */
    public function cannot_store_an_unregistered_novice_as_admin_coordinador()
    {
        $registration = Registration::factory()->forNovice()->create();
        $registration->invitation()->save(Invitation::factory()->create());

        $this->assertFalse($registration->isForCoordinator());
        $this->assertTrue($registration->isForNovice());

        $data = ['registration_id' => $registration->id];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-coordinators.store'), $data);

        $response->assertNotFound();

        $registration->refresh();
        $this->assertFalse($registration->isForCoordinator());
        $this->assertTrue($registration->isForNovice());
    }

    /** @test */
    public function cannot_store_an_unregistered_employer_as_admin_coordinador()
    {
        $registration = Registration::factory()->forEmployer()->create();
        $registration->invitation()->save(Invitation::factory()->create());

        $this->assertFalse($registration->isForCoordinator());
        $this->assertTrue($registration->isForEmployer());

        $data = ['registration_id' => $registration->id];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-coordinators.store'), $data);

        $response->assertNotFound();

        $registration->refresh();
        $this->assertFalse($registration->isForCoordinator());
        $this->assertTrue($registration->isForEmployer());
    }

    /** @test */
    public function guest_cannot_store_an_admin_coordinador()
    {
        $response = $this->post(route('admin-coordinators.store'), $this->data);

        $response->assertRedirect('login');
        $this->assertFalse($this->coordinator->isAdmin());
    }

    /** @test */
    public function user_without_role_cannot_store_an_admin_coordinador()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->post(route('admin-coordinators.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertFalse($this->coordinator->isAdmin());
    }

    /** @test */
    public function coordinator_cannot_store_an_admin_coordinador()
    {
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)
                         ->post(route('admin-coordinators.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertFalse($this->coordinator->isAdmin());
    }

    /** @test */
    public function instructor_cannot_store_an_admin_coordinador()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->post(route('admin-coordinators.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertFalse($this->coordinator->isAdmin());
    }

    /** @test */
    public function novice_cannot_store_an_admin_coordinador()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->post(route('admin-coordinators.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertFalse($this->coordinator->isAdmin());
    }

    /** @test */
    public function employer_cannot_store_an_admin_coordinador()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->post(route('admin-coordinators.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertFalse($this->coordinator->isAdmin());
    }

    /** @test */
    public function registration_id_is_required()
    {
        unset($this->data['registration_id']);

        $response = $this->actingAs($this->admin)
                         ->from($this->from)
                         ->post(route('admin-coordinators.store'), $this->data);

        $response->assertSessionHasErrors('registration_id')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function registration_must_exist()
    {
        $this->data['registration_id'] = 1234;

        $response = $this->actingAs($this->admin)
                         ->from($this->from)
                         ->post(route('admin-coordinators.store'), $this->data);

        $response->assertSessionHasErrors('registration_id')
                 ->assertRedirect($this->from);
    }
}
