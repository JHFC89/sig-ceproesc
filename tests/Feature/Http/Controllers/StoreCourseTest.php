<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;

class StoreCourseTest extends TestCase
{
    use RefreshDatabase;

    protected $data;

    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();

        $disciplines = Discipline::factory()
            ->count(10)
            ->state(new Sequence(
                ['basic' => true],
                ['basic' => false],
            ))
            ->create(['duration' => 10]);
        $this->data = [
            'name'          => 'test course name',
            'duration'      => 100,
            'disciplines'   => $disciplines->pluck('id')->toArray(),
        ];

        $this->coordinator = User::factory()
             ->hasRoles(1, ['name' => 'coordinator'])
             ->create();
    }

    /** @test */
    public function coordinator_can_store_a_course()
    {
        $this->withoutExceptionHandling();
        $disciplines = Discipline::factory()
            ->count(10)
            ->state(new Sequence(
                ['basic' => true],
                ['basic' => false],
            ))
            ->create(['duration' => 10]);
        $data = [
            'name'          => 'test course name',
            'duration'      => 100,
            'disciplines'   => $disciplines->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('courses.store'), $data);

        $response
            ->assertOk()
            ->assertViewIs('courses.show')
            ->assertViewHas('course')
            ->assertSee('test course name');
        $this->assertEquals(1, Course::count());
        $course = Course::first();
        $this->assertEquals('test course name', $course->name);
        $this->assertEquals(100, $course->duration);
        $this->assertEquals(100, $course->disciplines->sum('duration'));
    }

    /** @test */
    public function guest_cannot_store_a_course()
    {
        $response = $this->post(route('courses.store'), $this->data);

        $response->assertRedirect('login');
    }
    
    /** @test */
    public function user_without_role_cannot_store_a_course()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('courses.store'), $this->data);

        $response->assertUnauthorized();
    }
    
    /** @test */
    public function instructor_cannot_store_a_course()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this
            ->actingAs($instructor)
            ->post(route('courses.store'), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_store_a_course()
    {
        $novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();

        $response = $this
            ->actingAs($novice)
            ->post(route('courses.store'), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_store_a_course()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this
            ->actingAs($employer)
            ->post(route('courses.store'), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function name_is_required()
    {
        unset($this->data['name']);

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('courses.store'), $this->data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function name_must_be_a_string()
    {
        $this->data['name'] = 1;

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('courses.store'), $this->data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function name_must_be_unique()
    {
        Course::factory()->create(['name' => 'unique course name']);
        $this->data['name'] = 'unique course name';

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('courses.store'), $this->data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function duration_is_required()
    {
        unset($this->data['duration']);

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('courses.store'), $this->data);

        $response->assertSessionHasErrors('duration');
    }

    /** @test */
    public function duration_must_be_integer()
    {
        $this->data['duration'] = 'one';

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('courses.store'), $this->data);

        $response->assertSessionHasErrors('duration');
    }

    /** @test */
    public function duration_must_be_equal_to_the_sum_of_the_disciplines_duration()
    {
        $disciplines = Discipline::factory()
            ->count(10)
            ->create(['duration' => 5]);
        $this->data['disciplines'] = $disciplines->pluck('id');
        $this->data['duration'] = 100;

        $response = $this
            ->actingAs($this->coordinator)
            ->post(route('courses.store'), $this->data);

        $response->assertSessionHasErrors('disciplines');
    }

    /** @test */
    public function disciplines_are_required()
    {
        unset($this->data['disciplines']);

        $response = $this
            ->actingas($this->coordinator)
            ->post(route('courses.store'), $this->data);

        $response->assertsessionhaserrors('disciplines');
    }

    /** @test */
    public function disciplines_must_be_array()
    {
        $this->data['disciplines'] = 1;

        $response = $this
            ->actingas($this->coordinator)
            ->post(route('courses.store'), $this->data);

        $response->assertsessionhaserrors('disciplines');
    }
}
