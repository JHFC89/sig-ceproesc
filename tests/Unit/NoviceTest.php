<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NoviceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_presence_for_a_lesson()
    {
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();
        $lesson->registerFor($novice)->present()->complete();

        $presence = $novice->presentForLesson($lesson); 

        $this->assertTrue($presence);
    }

    /** @test */
    public function getting_presence_for_a_lesson_not_registered_yet_should_return_null()
    {
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();

        $presence = $novice->presentForLesson($lesson); 

        $this->assertNull($presence);
    }

    /** @test */
    public function getting_observation_for_a_lesson()
    {
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();
        $lesson->registerFor($novice)->present()->observation('test observation')->complete();

        $result = $novice->observationForLesson($lesson);

        $this->assertEquals('test observation', $result);
    }
}
