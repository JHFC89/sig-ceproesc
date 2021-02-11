<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Holiday;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreHolidayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function coordinator_can_store_a_holiday()
    {
        $data = [
            'holidays' => [
                [
                    'name'  => 'fake holiday',
                    'day'   => 1,
                    'month' => 2,
                    'year'  => 2021,
                ],
                [
                    'name'  => 'fakest holiday',
                    'day'   => 3,
                    'month' => 4,
                    'year'  => 2021,
                ],
            ]
        ];
        $coordinator = User::factory()
            ->hasRoles(['name' => 'coordinator'])
            ->create();

        $response = $this
            ->actingAs($coordinator)
            ->post(route('holidays.store'), $data);

        $response
            ->assertOk()
            ->assertViewIs('holidays.index')
            ->assertViewHas('holidays')
            ->assertSessionHas('status', 'Feriados cadastrados com sucesso!');
        $this->assertEquals(2, Holiday::count());
        $holidays = Holiday::oldest('date')->get();
        $this->assertEquals('fake holiday', $holidays[0]->name);
        $this->assertEquals('01/02/2021', $holidays[0]->formatted_date);
        $this->assertEquals('fakest holiday', $holidays[1]->name);
        $this->assertEquals('03/04/2021', $holidays[1]->formatted_date);
    }
}
