<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Discipline;
use Tests\TestCase;
use App\Models\User;
use App\Models\CourseClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewCourseClassTest extends TestCase
{
    use RefreshDatabase;

    protected $courseClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->courseClass = CourseClass::factory()->forCourse()->create();
    }

    /** @test */
    public function coordinator_can_view_a_course_class()
    {
        $this->withoutExceptionHandling();
        $coordinator = User::fakeCoordinator();
        $courseClass = CourseClass::factory()->forCourse()->create();

        $response = $this
            ->actingAs($coordinator)
            ->get(route('classes.show', ['courseClass' => $courseClass]));

        $response
            ->assertOk()
            ->assertViewIs('classes.show')
            ->assertViewHas('courseClass');
    }

    /** @test */
    public function can_see_a_link_to_create_lessons_if_not_created_already()
    {
        $coordinator = User::fakeCoordinator();
        $courseClass = CourseClass::factory()->forCourse()->create();

        $response = $this
            ->actingAs($coordinator)
            ->get(route('classes.show', ['courseClass' => $courseClass]));

        $response
            ->assertOk()
            ->assertSee(route('classes.lessons.create', [
                'courseClass' => $courseClass
            ]))
            ->assertDontSeeText(route('classes.lessons.index', [
                'courseClass' => $courseClass
            ]));
    }

    /** @test */
    public function cannot_see_a_link_to_create_lessons_if_it_is_already_created()
    {
        $coordinator = User::fakeCoordinator();
        $instructor = User::fakeInstructor();
        $discipline = Discipline::factory()->create();
        $courseClass = CourseClass::factory()->forCourse()->create();
        $date = now()->addDays(3);
        $lessons = [
            [
                'id'            => $date->format('Y-m-d') . '-first',
                'date'          => $date->format('Y-m-d'),
                'type'          => 'first',
                'duration'      => 2,
                'discipline_id' => $discipline->id,
                'instructor_id' => $instructor->id,
            ]
        ];
        $courseClass->createLessonsFromArray($lessons);

        $response = $this
            ->actingAs($coordinator)
            ->get(route('classes.show', ['courseClass' => $courseClass]));

        $response
            ->assertOk()
            ->assertSee(route('classes.lessons.index', [
                'courseClass' => $courseClass
            ]))
            ->assertDontSee(route('classes.lessons.create', [
                'courseClass' => $courseClass
            ]));
    }

    /** @test */
    public function guest_cannot_view_a_course_class()
    {
        $response = $this->get(route('classes.show', [
            'courseClass' => $this->courseClass
        ]));

        $response->assertRedirect('login');
    }
    
    /** @test */
    public function user_without_role_cannot_view_a_course_class()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('classes.show', [
            'courseClass' => $this->courseClass
        ]));

        $response->assertUnauthorized();
    }
    
    /** @test */
    public function instructor_cannot_view_a_course_class()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $response = $this->actingAs($instructor)->get(route('classes.show', [
            'courseClass' => $this->courseClass
        ]));

        $response->assertUnauthorized();
    }
    
    /** @test */
    public function novice_cannot_view_a_course_class()
    {
        $novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();

        $response = $this->actingAs($novice)->get(route('classes.show', [
            'courseClass' => $this->courseClass
        ]));

        $response->assertUnauthorized();
    }
    
    /** @test */
    public function employer_cannot_view_a_course_class()
    {
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();

        $response = $this->actingAs($employer)->get(route('classes.show', [
            'courseClass' => $this->courseClass
        ]));

        $response->assertUnauthorized();
    }
}
