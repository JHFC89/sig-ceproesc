<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewCompanyTest extends TestCase
{
    protected $company;

    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();

        $this->company = Company::factory()->create();

        $this->coordinator = User::fakeCoordinator();
    }

    /** @test */
    public function coordinator_can_view_a_company()
    {
        $response = $this->actingAs($this->coordinator)
                         ->get(route('companies.show', [
                             'company' => $this->company
                         ]));

        $response->assertOk()
                 ->assertViewHas('company')
                 ->assertViewIs('companies.show')
                 ->assertSee($this->company->name)
                 ->assertSee($this->company->cnpj)
                 ->assertSee($this->company->phones[0]->number)
                 ->assertSee($this->company->address->street)
                 ->assertSee($this->company->address->number)
                 ->assertSee($this->company->address->district)
                 ->assertSee($this->company->address->city)
                 ->assertSee($this->company->address->state)
                 ->assertSee($this->company->address->country)
                 ->assertSee($this->company->address->cep);
    }

    /** @test */
    public function guest_cannot_view_a_company()
    {
        $response = $this->get(route('companies.show', [
            'company' => $this->company
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_a_company()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('companies.show', [
                             'company' => $this->company
                         ]));


        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_a_company()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('companies.show', [
                             'company' => $this->company
                         ]));


        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_a_company()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('companies.show', [
                             'company' => $this->company
                         ]));


        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_a_company()
    {
        $employer = User::fakeInstructor();

        $response = $this->actingAs($employer)
                         ->get(route('companies.show', [
                             'company' => $this->company
                         ]));


        $response->assertUnauthorized();
    }
}
