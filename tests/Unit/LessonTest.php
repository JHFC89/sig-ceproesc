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
    public function can_check_assigned_instructor()
    {
        $lesson = Lesson::factory()->forInstructor()->create();
        $assignedInstructor = $lesson->instructor;
        $notAssignedInstructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $resultForAssignedInstructor = $lesson->isForInstructor($assignedInstructor);
        $resultForNotAssignedInstructor = $lesson->isForInstructor($notAssignedInstructor);
        
        $this->assertTrue($resultForAssignedInstructor);
        $this->assertFalse($resultForNotAssignedInstructor);
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
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();

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
    public function can_get_frequency_of_a_novice()
    {
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();
        $lesson->registerPresence($novice, 3);

        $frequency = $lesson->frequencyForNovice($novice);

        $this->assertEquals(3, $frequency);
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
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();

        $lesson->registerPresence($novice, 0);

        $this->assertEquals(0, $novice->lessons->first()->presence->frequency);
    }

    /** @test */
    public function can_update_a_novices_presence_with_different_value()
    {
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();
        $lesson->registerPresence($novice, 1);
        $this->assertEquals(1, $novice->lessons->first()->presence->frequency);

        $lesson->registerPresence($novice, 3);

        $this->assertEquals(3, $novice->fresh()->lessons->first()->presence->frequency);
    }

    /** @test */
    public function can_update_a_novices_presence_with_same_actual_value()
    {
        $lesson = Lesson::factory()->hasNovices(1)->create();
        $novice = $lesson->novices->first();
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
            $expectedResult[$novice->id] = $novice->frequencyForLesson($lesson);
            return $expectedResult;
        }, []);

        $result = $lesson->novicesFrequencyToJsonObject(); 

        $this->assertEquals(json_encode($expectedResult), $result);
    }

    /** @test */
    public function get_lessons_for_today()
    {
        $todayLessons = Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->count(3)->create();
        $yesterdayLessons = Lesson::factory()->notRegistered()->hasNovices(3)->count(3)->create(['date' => Carbon::parse('yesterday')]);
        $tomorrowLessons = Lesson::factory()->notRegistered()->hasNovices(3)->count(3)->create(['date' => Carbon::parse('tomorrow')]);

        $result = Lesson::today()->get();

        $this->assertEquals($todayLessons->pluck('id'), $result->pluck('id'));
        $this->assertNotEquals($yesterdayLessons->pluck('id'), $result->pluck('id'));
        $this->assertNotEquals($tomorrowLessons->pluck('id'), $result->pluck('id'));
    }

    /** @test */
    public function get_lessons_for_week()
    {
        $weekLessons = Lesson::factory()->forToday()->notRegistered()->hasNovices(3)->count(3)->create();
        $lastWeekLessons = Lesson::factory()->notRegistered()->hasNovices(3)->count(3)->create(['date' => Carbon::parse('-1 week')]);
        $nextWeekLessons = Lesson::factory()->notRegistered()->hasNovices(3)->count(3)->create(['date' => Carbon::parse('+1 week')]);

        $result = Lesson::week()->get();

        $this->assertEquals($weekLessons->pluck('id'), $result->pluck('id'));
        $this->assertNotEquals($lastWeekLessons->pluck('id'), $result->pluck('id'));
        $this->assertNotEquals($nextWeekLessons->pluck('id'), $result->pluck('id'));
    }
}
