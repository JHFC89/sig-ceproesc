<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NoviceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_frequency_for_a_lesson()
    {
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();
        $lesson->registerPresence($novice, 3);

        $frequency = $novice->frequencyForLesson($lesson); 

        $this->assertEquals(3, $lesson->frequencyForNovice($novice));
        $this->assertEquals($lesson->frequencyForNovice($novice), $frequency);
    }
}
