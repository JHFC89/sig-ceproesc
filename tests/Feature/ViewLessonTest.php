<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewLessonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_a_lesson_not_registered()
    {
        $date = Carbon::now();
        $lesson = Lesson::create([
            'instructor'    => 'John Doe',
            'date'          => $date,
            'class'         => '2021 - janeiro',
            'discipline'    => 'administração',
            'hourly_load'   => '123hr',
            'novice'        => 'Mary Jane'
        ]);

        $reponse = $this->get('lessons/' . $lesson->id);

        $reponse->assertOk();
        $reponse->assertSee('John Doe');
        $reponse->assertSee($date->format('d/m/Y'));
        $reponse->assertSee('2021 - janeiro');
        $reponse->assertSee('administração');
        $reponse->assertSee('123hr');
        $reponse->assertSee('Mary Jane');
    }

    /** @test */
    public function a_user_can_view_a_registered_lesson()
    {
        $date = Carbon::now();
        $lesson = Lesson::factory()->create([
            'register'      => 'Example register content',
        ]);

        $reponse = $this->get('lessons/' . $lesson->id);

        $reponse->assertSee('Example register content');
    }
}
