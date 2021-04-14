<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListCompanyTest extends TestCase
{
    protected $companies;

    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();

        $this->companies = Company::factory()->count(3)->create();

        $this->coordinator = User::fakeCoordinator();
    }

    /** @test */
    public function coordinator_can_view_a_list_of_companies()
    {
        $response = $this->actingAs($this->coordinator)
                         ->get(route('companies.index'));

        $response->assertOk()
                 ->assertViewIs('companies.index')
                 ->assertViewHas('companies')
                 ->assertSee($this->companies[0]->name)
                 ->assertSee($this->companies[1]->name)
                 ->assertSee($this->companies[2]->name);
    }

    /** @test */
    public function guest_cannot_view_a_list_of_companies()
    {
        $response = $this->get(route('companies.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function instructor_cannot_view_a_list_of_companies()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)->get(route('companies.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_a_list_of_companies()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)->get(route('companies.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_a_list_of_companies()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)->get(route('companies.index'));

        $response->assertUnauthorized();
    }
}
