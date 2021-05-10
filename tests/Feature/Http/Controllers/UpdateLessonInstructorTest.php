<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Discipline;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateLessonInstructorTest extends TestCase
{
    use RefreshDatabase;

    protected $coordinator;

    protected $lesson;

    protected $instructors;

    protected $data;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::fakeCoordinator();

        $this->instructors = User::factory()->count(2)
                                            ->hasRoles(1, [
                                                'name' => 'instructor'
                                            ])
                                            ->create();

        $this->lesson = Lesson::factory()->instructor($this->instructors[0])
                                         ->create();

        $this->lesson->discipline->attachInstructors($this->instructors[1]->id);

        $this->data = [
            'instructor' => $this->instructors[1]->id,
        ];
    }

    /** @test */
    public function coordinator_can_update_a_lesson_instructor()
    {
        $instructors = User::factory()->hasRoles(1, ['name' => 'instructor'])
                                      ->count(2)
                                      ->create();
        $lesson = Lesson::factory()->instructor($instructors[0])->create();
        $lesson->discipline->attachInstructors($instructors[1]->id);
        $this->assertTrue($lesson->isForInstructor($instructors[0]));
        $data = [
            'instructor' => $instructors[1]->id,
        ];

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-instructors.update', [
                             'lesson' => $lesson
                         ]), $data);

        $response->assertRedirect(route('lessons.edit', [
            'lesson' => $lesson,
        ]))->assertSessionHas('status', 'Instrutor da aula alterado com sucesso!');
        $lesson->refresh();
        $this->assertFalse($lesson->isForInstructor($instructors[0]));
        $this->assertTrue($lesson->isForInstructor($instructors[1]));
    }

    /** @test */
    public function cannot_update_the_instructor_of_a_registered_lesson()
    {
        $lesson = Lesson::factory()->registered()->create();
        
        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-instructors.update', [
                             'lesson' => $lesson
                         ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function cannot_update_to_an_instructor_that_dont_teach_the_lesson_discipline()
    {
        $disciplineA = Discipline::factory()->create();
        $disciplineB = Discipline::factory()->create();
        $lesson = Lesson::factory()->discipline($disciplineA)
                                   ->instructor($this->instructors[0])
                                   ->create();
        $disciplineB->attachInstructors($this->instructors[1]->id);
        $data = [
            'instructor' => $this->instructors[1]->id,
        ];

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-instructors.update', [
                             'lesson' => $lesson
                         ]), $data);

        $response->assertNotFound();
    }

    /** @test */
    public function guest_cannot_update_a_lesson_instructor()
    {
        $response = $this->patch(route('lesson-instructors.update', [
            'lesson' => $this->lesson
        ]), $this->data);

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_update_a_lesson_instructor()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('lesson-instructors.update', [
            'lesson' => $this->lesson
        ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_update_a_lesson_instructor()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->patch(route('lesson-instructors.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_update_a_lesson_instructor()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->patch(route('lesson-instructors.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_update_a_lesson_instructor()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->patch(route('lesson-instructors.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function updated_instructor_is_required()
    {
        $this->data['instructor'] = '';

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-instructors.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertSessionHasErrors('instructor');
    }

    /** @test */
    public function updated_instructor_must_be_a_integer()
    {
        $this->data['instructor'] = 'one';

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-instructors.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertSessionHasErrors('instructor');
    }

    /** @test */
    public function updated_instructor_must_exist()
    {
        $this->data['instructor'] = 1234;

        $response = $this->actingAs($this->coordinator)
                         ->patch(route('lesson-instructors.update', [
                             'lesson' => $this->lesson
                         ]), $this->data);

        $response->assertSessionHasErrors('instructor');
    }
}
