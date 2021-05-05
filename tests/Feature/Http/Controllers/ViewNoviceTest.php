<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Invitation, Registration, User, Company};

class ViewNoviceTest extends TestCase
{
    use RefreshDatabase;

    protected $coordinator;

    protected $company;

    protected $registration;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::fakeCoordinator();

        $this->company = Company::factory()->create();

        $this->registration = Registration::factory()->forNovice($this->company->id)->create([
            'name'          => 'Fake Novice',
        ]);

        $this->registration->invitation()->save(new Invitation([
            'email' => 'fakenovice@test.com',
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
    public function coordinator_can_view_a_novice_registration()
    {
        $response = $this->actingAs($this->coordinator)
                         ->get(route('novices.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertOk()
                 ->assertViewIs('novices.show')
                 ->assertViewHas('registration')
                 ->assertSee($this->registration->employer->name)
                 ->assertSee($this->registration->name)
                 ->assertSee($this->registration->email)
                 ->assertSee($this->registration->formatted_birthdate)
                 ->assertSee($this->registration->rg)
                 ->assertSee($this->registration->cpf)
                 ->assertSee($this->registration->ctps)
                 ->assertSee($this->registration->responsable_name)
                 ->assertSee($this->registration->responsable_cpf)
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
                         ->get(route('novices.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function cannot_view_a_registration_for_instructor()
    {
        $registration = Registration::factory()->forInstructor()->create();

        $response = $this->actingAs($this->coordinator)
                         ->get(route('novices.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function cannot_view_a_registration_for_employer()
    {
        $registration = Registration::factory()->forEmployer()->create();

        $response = $this->actingAs($this->coordinator)
                         ->get(route('novices.show', [
                             'registration' => $registration
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function guest_cannot_view_a_novice()
    {
        $response = $this->get(route('novices.show', [
            'registration' => $this->registration
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_a_novice()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('novices.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_a_novice()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('novices.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_a_novice()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('novices.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_can_view_his_novice()
    {
        $employer = User::fakeEmployer();
        $employer->registration->company()->associate($this->company)->save();
        $this->assertTrue($this->registration
                               ->employer
                               ->is($employer->company));

        $response = $this->actingAs($employer)
                         ->get(route('novices.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertOk();
    }

    /** @test */
    public function employer_cannot_view_a_novice()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('novices.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }
}
