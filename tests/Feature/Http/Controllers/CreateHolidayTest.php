<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateHolidayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function coordinator_can_create_holidays()
    {
        $coordinator = User::factory()
            ->hasRoles(['name' => 'coordinator'])
            ->create();
        
        $response = $this
            ->actingAs($coordinator)
            ->get(route('holidays.create'));

        $response
            ->assertOk()
            ->assertViewIs('holidays.create')
            ->assertSee(route('holidays.store'));
    }

    /** @test */
    public function guest_cannot_create_holidays()
    {
        $response = $this->get(route('holidays.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_create_holidays()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('holidays.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_create_holidays()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this->actingAs($instructor)->get(route('holidays.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_create_holidays()
    {
        $novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();

        $response = $this->actingAs($novice)->get(route('holidays.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_create_holidays()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this->actingAs($employer)->get(route('holidays.create'));

        $response->assertUnauthorized();
    }
}
