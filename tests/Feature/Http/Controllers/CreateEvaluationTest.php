<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateEvaluationTest extends TestCase
{
    use RefreshDatabase;

    protected $instructor;

    protected $lesson;

    protected function setUp():void
    {
        parent::setUp();

        $this->instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $this->lesson = Lesson::factory()->instructor($this->instructor)->create();
    }

    /** @test */
    public function instructor_can_view_the_create_evaluation_page()
    {
        $response = $this->actingAs($this->instructor)->get(route('lessons.evaluations.create', ['lesson' => $this->lesson]));

        $response
            ->assertOk()
            ->assertViewIs('evaluations.create')
            ->assertViewHas('lesson')
            ->assertSee(route('lessons.evaluations.store', ['lesson' => $this->lesson]));
    }

    /** @test */
    public function only_the_instructor_of_the_lesson_can_view_the_create_evaluation_page_for_it()
    {
        $instructorForAnotherLesson = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $response = $this->actingAs($instructorForAnotherLesson)->get(route('lessons.evaluations.create', ['lesson' => $this->lesson]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function cannot_create_an_evaluation_for_a_lesson_that_already_has_one()
    {
        $this->lesson->evaluation()->create(['label' => 'test label', 'description' => 'test description']);
        
        $response = $this->actingAs($this->instructor)->get(route('lessons.evaluations.create', ['lesson' => $this->lesson]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function cannot_create_an_evaluation_if_the_lesson_is_expired()
    {
        $expiredLesson = Lesson::factory()->expired()->instructor($this->instructor)->create();

        $response = $this->actingAs($this->instructor)->get(route('lessons.evaluations.create', ['lesson' => $expiredLesson]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function cannot_create_an_evaluation_if_the_lesson_is_registered()
    {
        $registeredLesson = Lesson::factory()->registered()->instructor($this->instructor)->create();

        $response = $this->actingAs($this->instructor)->get(route('lessons.evaluations.create', ['lesson' => $registeredLesson]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_view_the_create_evaluation_page()
    {
        $response = $this->get(route('lessons.evaluations.create', ['lesson' => $this->lesson]));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function coordinator_cannot_view_the_create_evaluation_page()
    {
        $coordinator = User::factory()->hasRoles(1, ['name' => 'coordinator'])->create();

        $response = $this->actingAs($coordinator)->get(route('lessons.evaluations.create', ['lesson' => $this->lesson]));

        $response->assertUnauthorized();
    }
        
    /** @test */
    public function novice_cannot_view_the_create_evaluation_page()
    {
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();

        $response = $this->actingAs($novice)->get(route('lessons.evaluations.create', ['lesson' => $this->lesson]));

        $response->assertUnauthorized();
    }
        
    /** @test */
    public function employer_cannot_view_the_create_evaluation_page()
    {
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])->create();

        $response = $this->actingAs($employer)->get(route('lessons.evaluations.create', ['lesson' => $this->lesson]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function user_cannot_view_the_create_evaluation_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('lessons.evaluations.create', ['lesson' => $this->lesson]));

        $response->assertUnauthorized();
    }
}
