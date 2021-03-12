<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Holiday;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListHolidayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function coordinator_can_view_a_list_of_holidays()
    {
        $holidays = Holiday::factory()
            ->count(3)
            ->state(new Sequence(
                ['local' => null],
                ['local' => 'araraquara'],
                ['local' => 'matão'],
            ))
            ->create();
        $coordinator = User::factory()
            ->hasRoles(['name' => 'coordinator'])
            ->create();

        $response = $this->actingAs($coordinator)->get(route('holidays.index'));

        $response
            ->assertOk()
            ->assertViewIs('holidays.index')
            ->assertViewHas('holidays')
            ->assertSee($holidays[0]->name)
            ->assertSee($holidays[0]->formatted_date)
            ->assertSee($holidays[0]->local)
            ->assertSee($holidays[1]->name)
            ->assertSee($holidays[1]->formatted_date)
            ->assertSee($holidays[1]->local)
            ->assertSee($holidays[2]->name)
            ->assertSee($holidays[2]->local);
    }

    /** @test */
    public function guest_cannot_view_a_list_of_holidays()
    {
        $response = $this->get(route('holidays.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_roles_cannot_view_a_list_of_holidays()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('holidays.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_view_a_list_of_holidays()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this->actingAs($instructor)->get(route('holidays.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_a_list_of_holidays()
    {
        $novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();

        $response = $this->actingAs($novice)->get(route('holidays.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_a_list_of_holidays()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this->actingAs($employer)->get(route('holidays.index'));

        $response->assertUnauthorized();
    }
}
