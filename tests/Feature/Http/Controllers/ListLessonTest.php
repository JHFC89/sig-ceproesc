<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListLessonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function coordinator_can_view_a_list_of_lessons_for_a_course_class()
    {
        $coordinator = User::fakeCoordinator();
        $courseClass = CourseClass::factory()->hasLessons(3)->create();

        $response = $this->actingAs($coordinator)
                         ->get(route('classes.lessons.index', [
                            'courseClass' => $courseClass
                        ]));

        $courseClass->load('lessons');
        $response->assertOk()
                 ->assertViewIs('lessons.index')
                 ->assertViewHas('lessons')
                 ->assertSee($courseClass->lessons[0]->formattedDate)
                 ->assertSee($courseClass->lessons[0]->discipline->name)
                 ->assertSee($courseClass->lessons[0]->instructor->name)
                 ->assertSee(route('lessons.show', [
                     'lesson' => $courseClass->lessons[0]
                 ]))
                 ->assertSee($courseClass->lessons[1]->formattedDate)
                 ->assertSee($courseClass->lessons[1]->discipline->name)
                 ->assertSee($courseClass->lessons[1]->instructor->name)
                 ->assertSee(route('lessons.show', [
                     'lesson' => $courseClass->lessons[1]
                 ]))
                 ->assertSee($courseClass->lessons[2]->formattedDate)
                 ->assertSee($courseClass->lessons[2]->discipline->name)
                 ->assertSee($courseClass->lessons[2]->instructor->name)
                 ->assertSee(route('lessons.show', [
                     'lesson' => $courseClass->lessons[2]
                 ]));
    }

    /** @test */
    public function guest_cannot_view_a_list_of_lessons_for_a_course_class()
    {
        $courseClass = CourseClass::factory()->hasLessons(3)->create();

        $response = $this->get(route('classes.lessons.index', [
            'courseClass' => $courseClass
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_a_list_of_lessons_for_a_course_class()
    {
        $courseClass = CourseClass::factory()->hasLessons(3)->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('classes.lessons.index', [
            'courseClass' => $courseClass
        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_can_view_a_list_of_lessons_for_a_course_class_he_is_assigned_to()
    {
        $courseClass = CourseClass::factory()->hasLessons(3)->create();
        $instructor = $courseClass->lessons->first()->instructor;

        $response = $this->actingAs($instructor)
                         ->get(route('classes.lessons.index', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertOk();
    }

    /** @test */
    public function instructor_cannot_view_a_list_of_lessons_for_a_course_class_he_is_not_assigned_to()
    {
        $courseClass = CourseClass::factory()->hasLessons(3)->create();
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('classes.lessons.index', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_can_view_a_list_of_lessons_for_a_course_class_he_is_enrolled_to()
    {
        $novice = User::fakeNovice();
        $courseClass = CourseClass::factory()->hasLessons(3)->create();
        $courseClass->subscribe($novice);

        $response = $this->actingAs($novice)
                         ->get(route('classes.lessons.index', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertOk();
    }

    /** @test */
    public function novice_cannot_view_a_list_of_lessons_for_a_course_class_he_is_not_enrolled_to()
    {
        $courseClass = CourseClass::factory()->hasLessons(3)->create();
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('classes.lessons.index', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_can_view_a_list_of_lessons_for_a_course_class_he_has_a_novice_enrolled_to()
    {
        $employer = User::fakeEmployer();
        $novice = User::fakeNovice();
        $employer->company->novices()->save($novice->registration);
        $courseClass = CourseClass::factory()->hasLessons(3)->create();
        $courseClass->subscribe($novice);

        $response = $this->actingAs($employer)
                         ->get(route('classes.lessons.index', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertOk();
    }

    /** @test */
    public function employer_cannot_view_a_list_of_lessons_for_a_course_class_he_has_no_novice_enrolled_to()
    {
        $courseClass = CourseClass::factory()->hasLessons(3)->create();
        $employer = User::fakeEmployer();
        $novice = User::fakeNovice();
        $employer->company->novices()->save($novice->registration);

        $response = $this->actingAs($employer)
                         ->get(route('classes.lessons.index', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertUnauthorized();
    }
}
