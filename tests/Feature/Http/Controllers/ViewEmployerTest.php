<?php

namespace Tests\Feature\Http\Controllers;

use App\Facades\InvitationCode;
use App\Models\Invitation;
use App\Models\Registration;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewEmployerTest extends TestCase
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

        $this->registration = Registration::factory()->create([
            'name'          => 'Fake Employer',
            'rg'            => '123-123-12',
            'company_id'    => $this->company->id,
        ]);

        $this->registration->invitation()->save(new Invitation([
            'email' => 'fakeemployer@test.com',
            'code' => InvitationCode::generate(),
        ]));
    }

    /** @test */
    public function coordinator_can_view_an_employer_registration()
    {
        $response = $this->actingAs($this->coordinator)
                         ->get(route('employers.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertOk()
                 ->assertViewIs('employers.show')
                 ->assertViewHas('registration')
                 ->assertSee($this->company->name)
                 ->assertSee($this->registration->name)
                 ->assertSee($this->registration->email)
                 ->assertSee($this->registration->rg);
    }

    /** @test */
    public function guest_cannot_view_an_employer()
    {
        $response = $this->get(route('employers.show', [
            'registration' => $this->registration
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_an_employer()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('employers.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_an_employer()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('employers.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_an_employer()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('employers.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_an_employer()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('employers.show', [
                             'registration' => $this->registration
                         ]));

        $response->assertUnauthorized();
    }
}
