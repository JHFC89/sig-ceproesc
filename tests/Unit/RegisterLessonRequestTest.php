<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Lesson;
use App\Models\RegisterLessonRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
}
