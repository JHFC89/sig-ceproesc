<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Evaluation;
use Illuminate\Support\Collection;
use App\Exceptions\NotANoviceException;
use App\Exceptions\AbsentNoviceException;
use App\Exceptions\NotInstructorException;
use App\Exceptions\NoviceNotEnrolledException;
use Illuminate\Validation\ValidationException;
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
        $evaluation->lesson->registerFor($novice)->present()->complete();

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
    public function trying_to_record_grade_for_a_novice_that_is_absent_from_the_lesson_should_throw_an_exception()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory()->hasNovices(1))->create();
        $evaluation->lesson->setTestData();
        $absentNovice = $evaluation->lesson->novices->first();

        try {
            $evaluation->recordGradeForNovice($absentNovice, 'a');
        } catch (AbsentNoviceException $exception) {
           $this->assertTrue(true); 
           return;
        }

        $this->fail('Trying to record a grade for a novice that is absent from the lesson should throw an exception');
    }

    /** @test */
    public function get_grade_for_a_novice()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory()->hasNovices(1))->create();
        $evaluation->lesson->setTestData();
        $novice = $evaluation->lesson->novices->first();
        $evaluation->lesson->registerFor($novice)->present()->complete();
        $evaluation->recordGradeForNovice($novice, 'a');

        $result = $evaluation->gradeForNovice($novice);

        $this->assertEquals('a', $result);
    }

    /** @test */
    public function validating_a_valid_grades_list_should_return_the_list_as_a_collection()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory()->hasNovices(2))->create();
        $evaluation->lesson->setTestData();
        $noviceA = $evaluation->lesson->novices->first();
        $noviceB = $evaluation->lesson->novices->last();
        $evaluation->lesson->registerFor($noviceA)->present()->complete();
        $evaluation->lesson->registerFor($noviceB)->present()->complete();
        $data = [
            $noviceA->id => 'a',
            $noviceB->id => 'b',
        ];

        $result = $evaluation->validateGradesList($data);

        $this->assertInstanceOf(Collection::class, $result);
        $expectedResult = collect([
            ['novice' => $noviceA, 'grade' => 'a'],
            ['novice' => $noviceB, 'grade' => 'b']
        ]);
        $this->assertEquals($expectedResult->first()['novice']->id, $result->first()['novice']->id);
        $this->assertEquals('a', $result->first()['grade']);
        $this->assertEquals($expectedResult->last()['novice']->id, $result->last()['novice']->id);
        $this->assertEquals('b', $result->last()['grade']);
    }

    /** @test */
    public function validating_a_grades_list_with_absent_novice_should_throw_a_validation_exception()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory()->hasNovices(2))->create();
        $evaluation->lesson->setTestData();
        $data = [
            $evaluation->lesson->novices->first()->id => 'a',
        ];

        try {
            $evaluation->validateGradesList($data);
        } catch (ValidationException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('A grades list with absent novice should throw a validation exception');
    }

    /** @test */
    public function can_record_a_list_of_grades()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory()->hasNovices(1))->create();
        $evaluation->lesson->setTestData();
        $novice = $evaluation->lesson->novices->first();
        $evaluation->lesson->registerFor($novice)->present()->complete();

        $evaluation->record([
            $evaluation->lesson->novices->first()->id => 'a',
        ]);

        $this->assertNotNull($evaluation->refresh()->recorded_at);
        $this->assertEquals('a', $evaluation->gradeForNovice($novice));
    }

    /** @test */
    public function can_check_it_is_recorded()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory()->hasNovices(1))->create();
        $evaluation->lesson->setTestData();
        $novice = $evaluation->lesson->novices->first();
        $evaluation->lesson->registerFor($novice)->present()->complete();
        $evaluation->record([
            $evaluation->lesson->novices->first()->id => 'a',
        ]);

        $result = $evaluation->isRecorded();

        $this->assertTrue($result);
    }

    /** @test */
    public function can_check_it_is_not_recorded()
    {
        $evaluation = Evaluation::factory()->for(Lesson::factory()->hasNovices(1))->create();
        $evaluation->lesson->setTestData();

        $result = $evaluation->isRecorded();

        $this->assertFalse($result);
    }
}
