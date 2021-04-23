<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateEmployerTest extends TestCase
{
    use RefreshDatabase;

    protected $coordinator;

    protected $company;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::fakeCoordinator();

        $this->company = Company::factory()->create();
    }

    /** @test */
    public function coordinator_can_create_an_employer()
    {
        $response = $this->actingAs($this->coordinator)
                         ->get(route('companies.employers.create', [
                             'company' => $this->company
                         ]));

        $response->assertOk()
                 ->assertViewIs('employers.create')
                 ->assertViewHas('company');
    }

    /** @test */
    public function guest_cannot_create_an_employer()
    {
        $response = $this->get(route('companies.employers.create', [
            'company' => $this->company
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_create_an_employer()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('companies.employers.create', [
                             'company' => $this->company
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_create_an_employer()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('companies.employers.create', [
                             'company' => $this->company
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_create_an_employer()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('companies.employers.create', [
                             'company' => $this->company
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_create_an_employer()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('companies.employers.create', [
                             'company' => $this->company
                         ]));

        $response->assertUnauthorized();
    }
}
