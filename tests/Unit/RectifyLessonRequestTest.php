<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Lesson;
use App\Models\RectifyLessonRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\RequestAlreadyReleasedException;

class RectifyLessonRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_a_request_for_a_lesson()
    {
        $lesson = Lesson::factory()->registered()->create();

        $request = RectifyLessonRequest::for($lesson, 'Test Justification');

        $this->assertEquals(1, RectifyLessonRequest::count());
        $this->assertEquals($lesson->id, $request->lesson->id);
        $this->assertEquals('Test Justification', $request->justification);
    }

    /** @test */
    public function cannot_create_request_for_a_lesson_that_is_not_registered()
    {
        $lesson = Lesson::factory()->notRegistered()->create();

        $request = RectifyLessonRequest::for($lesson, 'Test Justification');

        $this->assertEquals(0, RectifyLessonRequest::count());
    }

    /** @test */
    public function release_expired_lesson_to_register()
    {
        $lesson = Lesson::factory()->registered()->hasRectifications(1)->create();
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
        $lesson = Lesson::factory()->registered()->hasRectifications(1)->create();
        $request = $lesson->openRequest();
        $request->release();

        $result = $request->isReleased();

        $this->assertTrue($result);
    }

    /** @test */
    public function releasing_a_lesson_already_released_to_rectify_should_throw_an_exception()
    {
        $lesson = Lesson::factory()->registered()->hasRectifications(1)->create();
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
