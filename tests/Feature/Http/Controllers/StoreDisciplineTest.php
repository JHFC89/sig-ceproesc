<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreDisciplineTest extends TestCase
{
    use RefreshDatabase;

    protected $data;

    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();


        $this->coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create();

        $instructors = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->count(2)
            ->create();
        
        $this->data = [
            'name'          => 'Test Discipline',
            'basic'         => true,
            'duration'      => 30,
            'instructors'   => [
                $instructors->first()->id,
                $instructors->last()->id,
            ],
        ];
    }

    /** @test */
    public function coordinator_can_store_a_discipline()
    {
        $this->withoutExceptionHandling();

        $instructors = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->count(2)
            ->create();
        $data = [
            'name'          => 'Test Discipline',
            'basic'         => true,
            'duration'      => 30,
            'instructors'   => [
                $instructors->first()->id,
                $instructors->last()->id,
            ],
        ];

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('disciplines.store'), $data);

        $response
            ->assertOk()
            ->assertViewIs('disciplines.show');
        $this->assertEquals(1, Discipline::count());
        $discipline = Discipline::first();
        $this->assertEquals('Test Discipline', $discipline->name);
        $this->assertTrue($discipline->isBasic());
        $this->assertEquals(30, $discipline->duration);
        $this->assertEquals(
            $instructors->pluck('id'), 
            $discipline->instructors->pluck('id')
        );
    }

    /** @test */
    public function guest_cannot_store_a_discipline()
    {
        $response = $this->post(route('disciplines.store'), $this->data);

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_store_a_discipline()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('disciplines.store'), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_store_a_discipline()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this
            ->actingAs($instructor)
            ->post(route('disciplines.store'), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_store_a_discipline()
    {
        $novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();

        $response = $this
            ->actingAs($novice)
            ->post(route('disciplines.store'), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_store_a_discipline()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this
            ->actingAs($employer)
            ->post(route('disciplines.store'), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function name_is_required()
    {
        $data = $this->data;
        unset($data['name']);

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('disciplines.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function name_must_be_a_string()
    {
        $data = $this->data;
        $data['name'] = 1;

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('disciplines.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function basic_is_required()
    {
        $data = $this->data;
        unset($data['basic']);

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('disciplines.store'), $data);

        $response->assertSessionHasErrors('basic');
    }

    /** @test */
    public function basic_must_be_a_boolean()
    {
        $data = $this->data;
        $data['basic'] = 'true';

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('disciplines.store'), $data);

        $response->assertSessionHasErrors('basic');
    }

    /** @test */
    public function duration_is_required()
    {
        $data = $this->data;
        unset($data['duration']);

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('disciplines.store'), $data);

        $response->assertSessionHasErrors('duration');
    }

    /** @test */
    public function duration_must_be_a_integer()
    {
        $data = $this->data;
        $data['duration'] = 'twenty';

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('disciplines.store'), $data);

        $response->assertSessionHasErrors('duration');
    }

    /** @test */
    public function instructors_field_is_required()
    {
        $data = $this->data;
        unset($data['instructors']);

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('disciplines.store'), $data);

        $response->assertSessionHasErrors('instructors');
    }

    /** @test */
    public function instructors_field_must_be_an_array()
    {
        $data = $this->data;
        $data['instructors'] = '1, 2';

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('disciplines.store'), $data);

        $response->assertSessionHasErrors('instructors');
    }
}
