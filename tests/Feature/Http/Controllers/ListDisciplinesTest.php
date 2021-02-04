<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListDisciplinesTest extends TestCase
{
    use RefreshDatabase;

    protected $disciplines;

    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();

        $this->disciplines = Discipline::factory()
             ->hasInstructors(2)
             ->count(3)
             ->create();

        $this->coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create();
    }

    /** @test */
    public function coordinator_can_view_a_list_of_disciplines()
    {
        $response = $this
            ->actingAs($this->coordinator)
            ->get(route('disciplines.index'));

        $response
            ->assertOk()
            ->assertViewIs('disciplines.index')
            ->assertViewHas('disciplines')
            ->assertSee($this->disciplines[0]->name)
            ->assertSee($this->disciplines[1]->name)
            ->assertSee($this->disciplines[2]->name)
            ->assertSee(route('disciplines.show', [
                'discipline' => $this->disciplines[0]
            ]))
            ->assertSee(route('disciplines.show', [
                'discipline' => $this->disciplines[1]
            ]))
            ->assertSee(route('disciplines.show', [
                'discipline' => $this->disciplines[2]
            ]))
            ->assertSee(route('disciplines.edit', [
                'discipline' => $this->disciplines[0]
            ]))
            ->assertSee(route('disciplines.edit', [
                'discipline' => $this->disciplines[1]
            ]))
            ->assertSee(route('disciplines.edit', [
                'discipline' => $this->disciplines[2]
            ]));
    }
    
    /** @test */
    public function guest_cannot_view_list_of_disciplines()
    {
        $response = $this->get(route('disciplines.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_a_list_of_disciplines()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('disciplines.index'));

        $response->assertUnauthorized();
    }
    
    /** @test */
    public function instructor_cannot_view_a_list_of_disciplines()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this
            ->actingAs($instructor)
            ->get(route('disciplines.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_a_list_of_disciplines()
    {
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();

        $response = $this->actingAs($novice)->get(route('disciplines.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_a_list_of_disciplines()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this->actingAs($employer)->get(route('disciplines.index'));

        $response->assertUnauthorized();
    }
}
