<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Holiday;
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
}
