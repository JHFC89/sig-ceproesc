<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Discipline;
use Tests\TestCase;
use App\Models\User;
use App\Models\CourseClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateLessonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function coordinator_can_create_lessons_for_a_course_class()
    {
        $coordinator = User::fakeCoordinator();
        $courseClass = CourseClass::factory()->forCourse()->create();

        $response = $this
            ->actingAs($coordinator)
            ->get(route('classes.lessons.create', [
                'courseClass' => $courseClass
            ]));

        $response->assertOk()
                 ->assertViewIs('classes.lessons.create')
                 ->assertViewHas('courseClass');
    }

    /** @test */
    public function cannot_create_lessons_for_a_course_class_that_already_have_lessons()
    {
        $coordinator = User::fakeCoordinator();
        $instructor = User::fakeInstructor();
        $courseClass = CourseClass::factory()->forCourse()->create();
        $discipline = Discipline::factory()->create();
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
            ->get(route('classes.lessons.create', [
                'courseClass' => $courseClass
            ]));

        $response
            ->assertRedirect(route('classes.show', [
            'courseClass' => $courseClass
            ]))
            ->assertSessionHas('status', 'Aulas já cadastradas!');
    }

    /** @test */
    public function guest_cannot_create_lessons_for_a_course_class()
    {
        $courseClass = CourseClass::factory()->forCourse()->create();

        $response = $this->get(route('classes.lessons.create', [
            'courseClass' => $courseClass
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_create_lessons_for_a_course_class()
    {
        $user = User::factory()->create();
        $courseClass = CourseClass::factory()->forCourse()->create();

        $response = $this->actingAs($user)
                         ->get(route('classes.lessons.create', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_create_lessons_for_a_course_class()
    {
        $instructor = User::fakeInstructor();
        $courseClass = CourseClass::factory()->forCourse()->create();

        $response = $this->actingAs($instructor)
                         ->get(route('classes.lessons.create', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_create_lessons_for_a_course_class()
    {
        $novice = User::fakeNovice();
        $courseClass = CourseClass::factory()->forCourse()->create();

        $response = $this->actingAs($novice)
                         ->get(route('classes.lessons.create', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_create_lessons_for_a_course_class()
    {
        $employer = User::fakeEmployer();
        $courseClass = CourseClass::factory()->forCourse()->create();

        $response = $this->actingAs($employer)
                         ->get(route('classes.lessons.create', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertUnauthorized();
    }
}
