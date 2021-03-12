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
                    'local' => null,
                ],
                [
                    'name'  => 'fakest holiday',
                    'day'   => 3,
                    'month' => 4,
                    'year'  => 2021,
                    'local' => null,
                ],
                [
                    'name'  => 'fake local holiday',
                    'day'   => 5,
                    'month' => 6,
                    'year'  => 2021,
                    'local' => 'fake city of tiny lights',
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
        $this->assertEquals(3, Holiday::count());
        $holidays = Holiday::oldest('date')->get();
        $this->assertEquals('fake holiday', $holidays[0]->name);
        $this->assertEquals('01/02/2021', $holidays[0]->formatted_date);
        $this->assertEquals('nacional', $holidays[0]->local);
        $this->assertEquals('fakest holiday', $holidays[1]->name);
        $this->assertEquals('03/04/2021', $holidays[1]->formatted_date);
        $this->assertEquals('nacional', $holidays[1]->local);
        $this->assertEquals('fake local holiday', $holidays[2]->name);
        $this->assertEquals('05/06/2021', $holidays[2]->formatted_date);
        $this->assertEquals('fake city of tiny lights', $holidays[2]->local);
    }

    /** @test */
    public function guest_cannot_store_a_holiday()
    {
        $response = $this->post(route('holidays.store'), []);

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_store_a_holiday()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('holidays.store'), []);

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_store_a_holiday()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this
            ->actingAs($instructor)
            ->post(route('holidays.store'), []);

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_store_a_holiday()
    {
        $novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();

        $response = $this
            ->actingAs($novice)
            ->post(route('holidays.store'), []);

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_store_a_holiday()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this
            ->actingAs($employer)
            ->post(route('holidays.store'), []);

        $response->assertUnauthorized();
    }
}
