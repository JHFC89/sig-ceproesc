<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\User;
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
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create([
                'name' => 'John Doe'
            ]);
        $lesson = Lesson::factory()
            ->notRegistered()
            ->hasNovices(3)
            ->create([
                'instructor_id' => $instructor->id,
                'date'          => $date,
                'class'         => '2021 - janeiro',
                'discipline'    => 'administração',
                'hourly_load'   => '123hr',
        ]);
        extract($lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');

        $reponse = $this->get('lessons/' . $lesson->id);

        $reponse
            ->assertOk()
            ->assertSee('John Doe')
            ->assertSee($date->format('d/m/Y'))
            ->assertSee('2021 - janeiro')
            ->assertSee('administração')
            ->assertSee('123hr')
            ->assertSee($novice_0->name)
            ->assertSee($novice_1->name)
            ->assertSee($novice_2->name);
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
