<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Lesson;

class LessonTest extends TestCase
{
    /** @test */
    public function can_get_the_formatted_date()
    {
        $lesson = Lesson::factory()->make([
            'date' => Carbon::parse('2020, 11/02'),
        ]);

        $date = $lesson->formatted_date;

        $this->assertEquals('02/11/2020', $date);
    }

    /** @test */
    public function can_check_if_it_is_registered()
    {
        $lesson = Lesson::factory()->registered()->make([]);

        $this->assertTrue($lesson->isRegistered());
    }

    /** @test */
    public function can_check_if_it_is_not_registered()
    {
        $lesson = Lesson::factory()->notRegistered()->make([]);

        $this->assertFalse($lesson->isRegistered());
    }

    /** @test */
    public function can_check_if_it_is_for_today()
    {
        $lesson = Lesson::factory()->forToday()->make([]);

        $this->assertTrue($lesson->isForToday());
    }

    /** @test */
    public function can_check_if_it_is_not_for_today()
    {
        $lesson = Lesson::factory()->notForToday()->make([]);

        $this->assertFalse($lesson->isForToday());
    }

    /** @test */
    public function the_date_is_saved_as_utc_timezone()
    {
        $this->markTestSkipped();
        $date = now()->tz('America/Sao_Paulo')->format('d/m/Y H:i');
        $date = Carbon::createFromFormat('d/m/Y H:i', $date, 'America/Sao_Paulo');

        $lesson = Lesson::factory()->create([
            'date' => $date
        ]);

        $date->setTimezone('UTC');
        $this->assertEquals($date->format('d/m/Y H:i'), $lesson->date->format('d/m/Y H:i'));
    }
}
