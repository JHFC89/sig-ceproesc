<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Evaluation;
use App\Exceptions\NotInstructorException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EvaluationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_check_it_belongs_to_instructor()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory())->create();
        $instructor = $evaluation->lesson->instructor;

        $result = $evaluation->isForInstructor($instructor);

        $this->assertTrue($result);
    }

    /** @test */
    public function can_check_it_does_not_belong_to_instructor()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory())->create();
        $instructorNotOwner = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $result = $evaluation->isForInstructor($instructorNotOwner);

        $this->assertFalse($result);
    }

    /** @test */
    public function trying_to_check_it_belongs_to_an_user_that_is_not_an_instructor_should_throw_an_exception()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory())->create();
        $userNotInstructor = User::factory()->create();

        try {
            $evaluation->isForInstructor($userNotInstructor);
        } catch (NotInstructorException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to check if an evaluation belongs to an user that is not an instructor should throw an exception');
    }
}
