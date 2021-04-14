<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCompanyTest extends TestCase
{
    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::fakeCoordinator();
    }

    /** @test */
    public function coordinator_can_create_a_company()
    {
        $response = $this->actingAs($this->coordinator)
                         ->get(route('companies.create'));

        $response->assertOk()
                 ->assertViewIs('companies.create');
    }

    /** @test */
    public function guest_cannot_create_a_company()
    {
        $response = $this->get(route('companies.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_create_a_company()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('companies.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_create_a_company()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('companies.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_create_a_company()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('companies.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_create_a_company()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('companies.create'));

        $response->assertUnauthorized();
    }
}
