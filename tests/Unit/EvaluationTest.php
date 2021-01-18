<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Evaluation;
use App\Exceptions\NotANoviceException;
use App\Exceptions\NotInstructorException;
use App\Exceptions\NoviceNotEnrolledException;
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

    /** @test */
    public function record_grade_for_a_novice()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory()->hasNovices(1))->create();
        $evaluation->lesson->setTestData();
        $novice = $evaluation->lesson->novices->first();

        $evaluation->recordGradeForNovice($novice, 'a');

        $this->assertEquals('a', $evaluation->gradeForNovice($novice));
    }

    /** @test */
    public function trying_to_record_grade_for_an_user_that_is_not_a_novice_should_throw_an_exception()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory())->create();
        $notNovice = User::factory()->create();

        try {
            $evaluation->recordGradeForNovice($notNovice, 'a');
        } catch (NotANoviceException $exception) {
           $this->assertTrue(true); 
           return;
        }

        $this->fail('Trying to record a grade for an user that is not a novice should throw an exception');
    }

    /** @test */
    public function trying_to_record_grade_for_a_novice_that_is_not_enrolled_to_the_lesson_should_throw_an_exception()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory())->create();
        $notEnrolledNovice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();

        try {
            $evaluation->recordGradeForNovice($notEnrolledNovice, 'a');
        } catch (NoviceNotEnrolledException $exception) {
           $this->assertTrue(true); 
           return;
        }

        $this->fail('Trying to record a grade for a novice that is not enrolled to the lesson should throw an exception');
    }

    /** @test */
    public function get_grade_for_a_novice()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory()->hasNovices(1))->create();
        $evaluation->lesson->setTestData();
        $novice = $evaluation->lesson->novices->first();
        $evaluation->recordGradeForNovice($novice, 'a');

        $result = $evaluation->gradeForNovice($novice);

        $this->assertEquals('a', $result);
    }
}
