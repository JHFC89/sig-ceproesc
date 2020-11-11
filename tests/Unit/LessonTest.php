<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Exceptions\NoviceNotEnrolledException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LessonTest extends TestCase
{
    use RefreshDatabase;
    
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
        $lesson = Lesson::factory()->make([
            'register' => 'Fake register.',
            'registered_at' => Carbon::parse('+2 weeks'),
        ]);

        $this->assertTrue($lesson->isRegistered());
        $this->assertNotNull($lesson->registered_at);
    }

    /** @test */
    public function can_check_if_it_is_not_registered()
    {
        $lesson = Lesson::factory()->make([
            'registered_at' => null,
        ]);

        $this->assertFalse($lesson->isRegistered());
        $this->assertNull($lesson->registered_at);
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
    
    /** @test */
    public function can_enroll_a_novice()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();
        
        $lesson->enroll($novice);
        $this->assertNotNull($novice->lessons);
        $this->assertEquals($lesson->id, $novice->lessons->first()->id);
    }

    /** @test */
    public function can_check_a_novice_is_enrolled()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();
        
        $lesson->enroll($novice);

        $this->assertTrue($lesson->isEnrolled($novice));
    }

    /** @test */
    public function can_check_a_novice_is_not_enrolled()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();
        
        $this->assertFalse($lesson->isEnrolled($novice));
    }

    /** @test */
    public function can_register_the_presence_of_an_enrolled_novice()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $lesson->enroll($novice);

        $lesson->registerPresence($novice, 3);

        $this->assertEquals(3, $novice->lessons->first()->presence->frequency);
    }

    /** @test */
    public function cannot_register_the_presence_of_a_novice_that_is_not_enrolled()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();

        try {
            $lesson->registerPresence($novice, 3);
        } catch (NoviceNotEnrolledException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to register the presence of a novice that is not enrolled to the lesson should throw a exception');
    }

    /** @test */
    public function the_frequency_of_enrolled_novice_but_with_no_presence_registered_should_be_null()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $lesson->enroll($novice);

        $this->assertNull($novice->lessons->first()->presence->frequency);
    }

    /** @test */
    public function the_frequency_of_enrolled_novice_registered_as_not_present_should_return_zero()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $lesson->enroll($novice);

        $lesson->registerPresence($novice, 0);

        $this->assertEquals(0, $novice->lessons->first()->presence->frequency);
    }

    /** @test */
    public function can_update_a_novices_presence_with_different_value()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $lesson->enroll($novice);
        $lesson->registerPresence($novice, 1);
        $this->assertEquals(1, $novice->lessons->first()->presence->frequency);

        $lesson->registerPresence($novice, 3);

        $this->assertEquals(3, $novice->fresh()->lessons->first()->presence->frequency);
    }

    /** @test */
    public function can_update_a_novices_presence_with_same_actual_value()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $lesson->enroll($novice);
        $lesson->registerPresence($novice, 3);
        $this->assertEquals(3, $novice->lessons->first()->presence->frequency);

        $lesson->registerPresence($novice, 3);

        $this->assertEquals(3, $novice->fresh()->lessons->first()->presence->frequency);
    }

    /** @test */
    public function can_get_formatted_json_for_novices_with_no_presence_registered()
    {
        $lesson = Lesson::factory()->hasNovices(5)->create();
        $novicesIds = $lesson->novices->pluck('id');
        $expectedResult = $lesson->novices->reduce(function ($expectedResult, $novice) {
            $expectedResult[$novice->id] = 3;
            return $expectedResult;
        }, []);

        $result = $lesson->novicesFrequencyToJsonObject(); 

        $this->assertEquals(json_encode($expectedResult), $result);
    }

    /** @test */
    public function can_get_formatted_json_for_novices_with_different_frequencies_registered()
    {
        $lesson = Lesson::factory()->hasNovices(4)->create();
        $novices = $lesson->novices;
        $novices->each(function ($novice, $key) use ($lesson) {
            $lesson->registerPresence($novice, $key);
        });
        $expectedResult = $novices->reduce(function ($expectedResult, $novice) use ($lesson) {
            $expectedResult[$novice->id] = $novice->lessons->find($lesson)->presence->frequency;
            return $expectedResult;
        }, []);

        $result = $lesson->novicesFrequencyToJsonObject(); 

        $this->assertEquals(json_encode($expectedResult), $result);
    }
}
