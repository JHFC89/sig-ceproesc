<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateDisciplineTest extends TestCase
{
    /** @test */
    public function user_can_view_the_create_page()
    {
        $this->withoutExceptionHandling();
        $coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create();

        $response = $this
            ->actingAs($coordinator)
            ->get(route('disciplines.create'));

        $response
            ->assertOk()
            ->assertViewIs('disciplines.create')
            ->assertViewHas('instructors');
    }

    /** @test */
    public function guest_cannot_view_the_create_page()
    {
        $response = $this->get(route('disciplines.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_the_create_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('disciplines.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_the_create_page()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this
            ->actingAs($instructor)
            ->get(route('disciplines.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_the_create_page()
    {
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();

        $response = $this->actingAs($novice)->get(route('disciplines.create'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_the_create_page()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this
            ->actingAs($employer)
            ->get(route('disciplines.create'));

        $response->assertUnauthorized();
    }
}
