<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use Tests\Traits\LessonTestData;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterPendingLessonRequestTest extends TestCase
{
    use RefreshDatabase, LessonTestData;

    private $instructor;

    protected function setUp():void
    {
        parent::setUp();

        $this->instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
    }

    /** @test */
    public function registering_an_expired_lesson_will_set_the_corresponding_request_to_solved()
    {
        $lesson = Lesson::factory()
            ->expired()
            ->notRegistered()
            ->hasNovices(2)
            ->hasRequests(1, ['rectification' => false])
            ->instructor($this->instructor)
            ->create();
        $request = $lesson->openRequest();
        $request->release();
        $data = $this->data()->novices($lesson->novices)->get();
        
        $response = $this->actingAs($this->instructor, 'api')->postJson('api/lessons/register/' . $lesson->id, $data);
        $lesson->refresh();

        $response->assertStatus(201);
        $this->assertTrue($lesson->isRegistered());
        $this->assertFalse($lesson->hasOpenRequest());
        $this->assertFalse($lesson->hasPendingRequest());
        $this->assertTrue($lesson->hasSolvedRequest());
        $this->assertTrue($request->fresh()->isSolved());
    }

    /** @test */
    public function rectifying_a_registered_lesson_will_set_the_corresponding_request_to_solved()
    {
        $lesson = Lesson::factory()
            ->registered()
            ->hasNovices(2)
            ->hasRequests(1)
            ->instructor($this->instructor)
            ->create();
        $request = $lesson->openRequest();
        $request->release();
        $data = $this->data()->novices($lesson->novices)->get();
        
        $response = $this->actingAs($this->instructor, 'api')->postJson('api/lessons/register/' . $lesson->id, $data);
        $lesson->refresh();

        $response->assertStatus(201);
        $this->assertTrue($lesson->isRegistered());
        $this->assertFalse($lesson->hasOpenRequest());
        $this->assertFalse($lesson->hasPendingRequest());
        $this->assertTrue($lesson->hasSolvedRequest());
        $this->assertTrue($request->fresh()->isSolved());
    }
}
