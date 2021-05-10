<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Lesson;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateLessonDateTest extends TestCase
{
    use RefreshDatabase;

    protected $coordinator;

    protected $lesson;

    protected $data;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::fakeCoordinator();

        $this->lesson = Lesson::factory()->create([
            'date' => '2021-1-1',
        ]);

        $this->data = [
            'date' => [
                'day'   => 2,
                'month' => 2,
                'year'  => 2021,
            ],
        ];
    }

    /** @test */
    public function coordinator_can_update_a_lesson_date()
    {
        $lesson = Lesson::factory()->create([
            'date' => '2021-1-1',
        ]);
        $data = [
            'date' => [
                'day'   => 2,
                'month' => 2,
                'year'  => 2021,
            ],
        ];

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-dates.update', [
                             'lesson' => $lesson
                         ]), $data);

        $response->assertRedirect(route('lessons.edit', [
            'lesson' => $lesson,
        ]))->assertSessionHas('status', 'Data da aula alterada com sucesso!');
        $this->assertEquals('02/02/2021', $lesson->refresh()->formatted_date);
    }

    /** @test */
    public function cannot_update_the_date_of_a_registered_lesson()
    {
        $lesson = Lesson::factory()->registered()->create();
        
        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-dates.update', [
                             'lesson' => $lesson
                         ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_update_a_lesson_date()
    {
        $response = $this->patch(route('lesson-dates.update', [
            'lesson' => $this->lesson
        ]), $this->data);

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_update_a_lesson_date()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('lesson-dates.update', [
            'lesson' => $this->lesson
        ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_update_a_lesson_date()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->patch(route('lesson-dates.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_update_a_lesson_date()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->patch(route('lesson-dates.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_update_a_lesson_date()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->patch(route('lesson-dates.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function updated_date_is_required()
    {
        unset($this->data['date']);

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-dates.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertSessionHasErrors('date');
    }

    /** @test */
    public function updated_date_day_is_required()
    {
        $this->data['date']['day'] = '';

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-dates.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertSessionHasErrors('date.day');
    }

    /** @test */
    public function updated_date_day_must_be_a_integer()
    {
        $this->data['date']['day'] = 'one';

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-dates.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertSessionHasErrors('date.day');
    }

    /** @test */
    public function updated_date_month_is_required()
    {
        $this->data['date']['month'] = '';

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-dates.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertSessionHasErrors('date.month');
    }

    /** @test */
    public function updated_date_month_must_be_a_integer()
    {
        $this->data['date']['month'] = 'one';

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-dates.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertSessionHasErrors('date.month');
    }

    /** @test */
    public function updated_date_year_is_required()
    {
        $this->data['date']['year'] = '';

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-dates.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertSessionHasErrors('date.year');
    }

    /** @test */
    public function updated_date_year_must_be_a_integer()
    {
        $this->data['date']['year'] = 'one';

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-dates.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertSessionHasErrors('date.year');
    }
}
