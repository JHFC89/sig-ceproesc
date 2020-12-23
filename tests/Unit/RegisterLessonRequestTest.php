<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Lesson;
use App\Models\RegisterLessonRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\RequestAlreadyReleasedException;

class RegisterLessonRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_a_request_for_a_lesson()
    {
        $lesson = Lesson::factory()->create();

        $request = RegisterLessonRequest::for($lesson, 'Test Justification');

        $this->assertEquals(1, RegisterLessonRequest::count());
        $this->assertEquals($lesson->id, $request->lesson->id);
        $this->assertEquals('Test Justification', $request->justification);
    }

    /** @test */
    public function can_check_instructor_requester()
    {
        $lesson = Lesson::factory()->create();
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
        $lesson = Lesson::factory()->create();
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
}
