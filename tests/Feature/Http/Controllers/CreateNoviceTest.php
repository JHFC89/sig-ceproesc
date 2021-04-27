<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\{User, Company};
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateNoviceTest extends TestCase
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
    public function coordinator_can_create_a_novice()
    {
        $response = $this->actingAs($this->coordinator)
                         ->get(route('companies.novices.create', [
                             'company' => $this->company
                         ]));

        $response->assertOk()
                 ->assertViewIs('novices.create')
                 ->assertViewHas('company');
    }

    /** @test */
    public function guest_cannot_create_a_novice()
    {
        $response = $this->get(route('companies.novices.create', [
            'company' => $this->company
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_create_a_novice()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('companies.novices.create', [
                             'company' => $this->company
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_create_a_novice()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('companies.novices.create', [
                             'company' => $this->company
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_create_a_novice()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('companies.novices.create', [
                             'company' => $this->company
                         ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_create_a_novice()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('companies.novices.create', [
                             'company' => $this->company
                         ]));

        $response->assertUnauthorized();
    }
}
