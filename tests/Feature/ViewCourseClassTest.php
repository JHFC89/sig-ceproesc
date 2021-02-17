<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\CourseClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewCourseClassTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function coordinator_can_view_a_course_class()
    {
        $coordinator = User::factory()
            ->hasRoles(['name' => 'coordinator'])
            ->create();
        $courseClass = CourseClass::factory()->forCourse()->create();

        $response = $this
            ->actingAs($coordinator)
            ->get(route('classes.show', ['courseClass' => $courseClass]));

        $response
            ->assertOk()
            ->assertViewIs('classes.show')
            ->assertViewHas('courseClass');
    }
}
