<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Lesson;
use App\Models\RegisterLessonRequest;
use App\Exceptions\NotExpectedLessonException;
use App\Exceptions\RequestNotReleasedException;
use App\Exceptions\LessonNotRegisteredException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\RequestAlreadyReleasedException;

class RegisterLessonRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_a_request_for_a_lesson()
    {
        $lesson = Lesson::factory()->expired()->create();

        $request = RegisterLessonRequest::for($lesson, 'Test Justification');

        $this->assertEquals(1, RegisterLessonRequest::count());
        $this->assertEquals($lesson->id, $request->lesson->id);
        $this->assertEquals('Test Justification', $request->justification);
    }

    /** @test */
    public function cannot_create_request_for_a_lesson_that_is_not_expired()
    {
        $lesson = Lesson::factory()->create();

        $request = RegisterLessonRequest::for($lesson, 'Test Justification');

        $this->assertEquals(0, RegisterLessonRequest::count());
    }

    /** @test */
    public function can_check_instructor_requester()
    {
        $lesson = Lesson::factory()->expired()->create();
        $request = RegisterLessonRequest::for($lesson, 'Test Justification');
        $requester = $request->lesson->instructor;
        $notRequester = \App\Models\User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $resultForRequester = $request->isForInstructor($requester);
        $resultForNotRequester = $request->isForInstructor($notRequester);
        
        $this->assertTrue($resultForRequester);
        $this->assertFalse($resultForNotRequester);
    }

    /** @test */
    public function get_requester()
    {
        $lesson = Lesson::factory()->expired()->create();
        $request = RegisterLessonRequest::for($lesson, 'Test Justification');
        
        $result = $request->instructor;

        $this->assertEquals($lesson->instructor->id, $result->id);
    }

    /** @test */
    public function release_expired_lesson_to_register()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();

        $request->release();

        $request->refresh();
        $this->assertNotNull($request->released_at);
        $this->assertInstanceOf(Carbon::class, $request->released_at);
        $this->assertEquals(now()->format('d-m-Y'), $request->released_at->format('d-m-Y'));
    }

    /** @test */
    public function can_check_request_is_released()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();
        $request->release();

        $result = $request->isReleased();

        $this->assertTrue($result);
    }

    /** @test */
    public function can_check_request_is_not_released()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();

        $result = $request->isReleased();

        $this->assertFalse($result);
    }

    /** @test */
    public function releasing_a_lesson_already_released_should_throw_an_exception()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();
        $request->release();

        try {
            $request->release();
        } catch (RequestAlreadyReleasedException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to released a lesson already released should throw an exception');
    }

    /** @test */
    public function can_be_solved()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();
        $request->release();
        $lesson->register();

        $request->solve($lesson);

        $this->assertEquals($lesson->registered_at, $request->fresh()->solved_at);
    }

    /** @test */
    public function cannot_solve_a_request_that_is_not_released()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();
        $lesson->register();

        try {
            $request->solve($lesson);
        } catch (RequestNotReleasedException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to solve a request that is not released should throw an exception');
    }

    /** @test */
    public function cannot_solve_a_request_if_it_does_not_belong_to_the_given_lesson()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();
        $request->release();
        $otherLesson = Lesson::factory()->expired()->hasRequests(1)->create();

        try {
            $request->solve($otherLesson);
        } catch (NotExpectedLessonException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to solve a request that does not belong to the given lesson should throw an exception');
    }

    /** @test */
    public function cannot_be_solved_if_lesson_is_not_registered()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();
        $request->release();

        try {
            $request->solve($lesson);
        } catch (LessonNotRegisteredException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to solve a request which lesson is not registered should throw an exception');
    }

    /** @test */
    public function can_check_request_is_solved()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();
        $request->release();
        $lesson->register();
        $request->solve($lesson);

        $result = $request->isSolved();

        $this->assertTrue($result);
    }

    /** @test */
    public function can_check_request_is_not_solved()
    {
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $request = $lesson->openRequest();
        $request->release();
        $lesson->register();

        $result = $request->isSolved();

        $this->assertFalse($result);
    }
}
