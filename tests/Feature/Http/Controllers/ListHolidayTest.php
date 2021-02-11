<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Holiday;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListHolidayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function coordinator_can_view_a_list_of_holidays()
    {
        $holidays = Holiday::factory()->count(3)->create();
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
            ->assertSee($holidays[1]->name)
            ->assertSee($holidays[1]->formatted_date)
            ->assertSee($holidays[2]->name)
            ->assertSee($holidays[2]->formatted_date);
    }
}
