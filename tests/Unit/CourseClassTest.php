<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\CourseClass;
use App\Exceptions\NotANoviceException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseClassTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function subscribe_novice_to_course_class()
    {
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();
        $courseClass = CourseClass::factory()->create(['name' => 'julho - 2020']);

        $courseClass->subscribe($novice);

        $this->assertEquals('julho - 2020', $novice->class);
    }

    /** @test */
    public function subscribe_a_user_that_is_not_a_novice_should_throw_a_exception()
    {
        $user = User::factory()->create();
        $courseClass = CourseClass::factory()->create();

        try {
            $courseClass->subscribe($user);
        } catch (NotANoviceException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to subscribe a user that is not a novice to a course class should throw an exception');
    }
}
