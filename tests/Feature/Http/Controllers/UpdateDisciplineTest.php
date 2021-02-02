<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateDisciplineTest extends TestCase
{
    use RefreshDatabase;

    protected $discipline;

    protected $data;

    protected function setUp():void
    {
        parent::setUp();

        $this->discipline = Discipline::factory()->hasInstructors(2)->create();
        $this->discipline->instructors->first()->turnIntoInstructor();
        $this->discipline->instructors->last()->turnIntoInstructor();

        $this->data = [
            'name'  => 'Updated Test Name',
            'basic' => false,
            'duration' => 10,
            'instructors' => [
                $this->discipline->instructors->first()->id,
            ],
        ];
    }

    /** @test */
    public function can_update_a_discipline()
    {
        $coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create();
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();
        $data = [
            'name'  => 'Updated Test Name',
            'basic' => false,
            'duration' => 10,
            'instructors' => [
                $this->discipline->instructors->first()->id,
                $instructor->id,
            ],
        ];

        $response = $this
            ->actingAs($coordinator)
            ->patch(route(
                'disciplines.update',
                ['discipline' => $this->discipline]
            ), $data);

        $response
            ->assertOk()
            ->assertViewIs('disciplines.show')
            ->assertViewHas('discipline')
            ->assertSessionHas('status', 'Disciplina atualizada com sucesso!')
            ->assertSee('Disciplina atualizada com sucesso!');
        $this->discipline->refresh();
        $this->assertEquals('Updated Test Name', $this->discipline->name);
        $this->assertTrue($this->discipline->isSpecific());
        $this->assertEquals(10, $this->discipline->duration);
        $this->assertEquals(
            $data['instructors'],
            $this->discipline->instructors->pluck('id')->toArray()
        );
    }

    /** @test */
    public function guest_cannot_update_a_discipline()
    {
        $response = $this
            ->patch(route(
                'disciplines.update',
                ['discipline' => $this->discipline]
            ), $this->data);

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_update_a_discipline()
    {
        $user = User::factory()->create();
        $response = $this
            ->actingAs($user)
            ->patch(route(
                'disciplines.update',
                ['discipline' => $this->discipline]
            ), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_update_a_discipline()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();
        $response = $this
            ->actingAs($instructor)
            ->patch(route(
                'disciplines.update',
                ['discipline' => $this->discipline]
            ), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_update_a_discipline()
    {
        $novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();
        $response = $this
            ->actingAs($novice)
            ->patch(route(
                'disciplines.update',
                ['discipline' => $this->discipline]
            ), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_update_a_discipline()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();
        $response = $this
            ->actingAs($employer)
            ->patch(route(
                'disciplines.update',
                ['discipline' => $this->discipline]
            ), $this->data);

        $response->assertUnauthorized();
    }
}
