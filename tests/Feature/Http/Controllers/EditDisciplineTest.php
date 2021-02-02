<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditDisciplineTest extends TestCase
{
    use RefreshDatabase;

    protected $discipline;

    protected function setUp():void
    {
        parent::setUp();

        $this->discipline = Discipline::factory()->hasInstructors(1)->create();
        $this->discipline->instructors->first()->turnIntoInstructor();
    }

    /** @test */
    public function coordinator_can_view_the_edit_page()
    {
        $coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create();

        $response = $this
            ->actingAs($coordinator)
            ->get(route(
                'disciplines.edit', 
                ['discipline' => $this->discipline]
            ));

        $response
            ->assertOk()
            ->assertViewIs('disciplines.edit')
            ->assertViewHas('discipline')
            ->assertViewHas('instructors')
            ->assertSee($this->discipline->name)
            ->assertSee($this->discipline->duration)
            ->assertSee('básico')
            ->assertSee($this->discipline->instructors->first()->name);
    }

    /** @test */
    public function guest_cannot_view_edit_page()
    {
        $response = $this
            ->get(route(
                'disciplines.edit', 
                ['discipline' => $this->discipline]
            ));

        $response->assertRedirect('login');
    }

    /** @test */
    public function instructor_cannot_view_the_edit_page()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this
            ->actingAs($instructor)
            ->get(route(
                'disciplines.edit', 
                ['discipline' => $this->discipline]
            ));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_the_edit_page()
    {
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();

        $response = $this
            ->actingAs($novice)
            ->get(route(
                'disciplines.edit', 
                ['discipline' => $this->discipline]
            ));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_the_edit_page()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this
            ->actingAs($employer)
            ->get(route(
                'disciplines.edit', 
                ['discipline' => $this->discipline]
            ));

        $response->assertUnauthorized();
    }
}
