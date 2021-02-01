<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewDisciplineTest extends TestCase
{
    use RefreshDatabase;

    protected $discipline;

    protected function setUp():void
    {
        parent::setUp();

        $this->discipline = Discipline::factory()->create();
    }

    /** @test */
    public function coordinator_can_view_a_discipline()
    {
        $coodinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create();
        $discipline = Discipline::factory()->hasInstructors(2)->create([
            'name' => 'Comunicação',
            'basic' => true,
            'duration' => 30,
        ]);
        $instructors = $discipline->instructors;

        $response = $this
            ->actingAs($coodinator)
            ->get(route('disciplines.show', $discipline));

        $response
            ->assertOk()
            ->assertViewIs('disciplines.show')
            ->assertSee('Comunicação')
            ->assertSee('básico')
            ->assertSee('30 hr')
            ->assertSee($instructors->first()->name)
            ->assertSee($instructors->last()->name);
    }

    /** @test */
    public function guest_cannot_view_a_discipline()
    {
        $response = $this->get(route('disciplines.show', $this->discipline));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_wihtout_role_cannot_view_a_discipline()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('disciplines.show', $this->discipline));

        $response->assertUnauthorized();
    }
    
    /** @test */
    public function instructor_cannot_view_a_discipline()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this
            ->actingAs($instructor)
            ->get(route('disciplines.show', $this->discipline));

        $response->assertUnauthorized();
    }
    
    /** @test */
    public function novice_cannot_view_a_discipline()
    {
        $novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();

        $response = $this
            ->actingAs($novice)
            ->get(route('disciplines.show', $this->discipline));

        $response->assertUnauthorized();
    }
    //employer
    /** @test */
    public function employer_cannot_view_a_discipline()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this
            ->actingAs($employer)
            ->get(route('disciplines.show', $this->discipline));

        $response->assertUnauthorized();
    }
}
