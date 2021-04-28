<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CourseClass;
use App\Models\Discipline;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected $courseClass;

    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();

        $this->courseClass = CourseClass::factory()->create();

        $this->coordinator = User::fakeCoordinator();
    }

    private function lessonsArray()
    {
        $instructorA = User::fakeInstructor();
        $instructorB = User::fakeInstructor();

        $disciplineA = Discipline::factory()->create();
        $disciplineB = Discipline::factory()->create();

        $dateA = now()->addDays(1)->format('Y-m-d');
        $dateB = now()->addDays(2)->format('Y-m-d');

        return [
            [
                'id' => 'lesson A',
                'date' => $dateA,
                'type' => 'first',
                'duration' => 2,
                'instructor_id' => $instructorA->id,
                'discipline_id' => $disciplineA->id,
            ],
            [
                'id' => 'Lesson B',
                'date' => $dateB,
                'type' => 'second',
                'duration' => 3,
                'instructor_id' => $instructorB->id,
                'discipline_id' => $disciplineB->id,
            ],
        ];
    }

    /** @test */
    public function coordinator_can_view_the_page_to_subscribe_a_novice_to_a_course_class()
    {
        $courseClass = CourseClass::factory()->create();
        $courseClass->createLessonsFromArray($this->lessonsArray());
        $availableNovices = collect([
            User::fakeNovice(),
            User::fakeNovice(),
            User::fakeNovice(),
        ]);
        $unavailableNovices = collect([
            User::fakeNovice(),
            User::fakeNovice(),
            User::fakeNovice(),
        ]);
        $unavailableNovices->each(function ($novice) use ($courseClass) {
            $courseClass->subscribe($novice);
        });

        $response = $this->actingAs($this->coordinator)
                         ->get(route('classes.subscriptions.create', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertOk()
                 ->assertViewIs('subscriptions.create')
                 ->assertViewHas('courseClass')
                 ->assertViewHas('availableNovices')
                 ->assertSee($courseClass->name)
                 ->assertSee($availableNovices[0]->name)
                 ->assertSee($availableNovices[1]->name)
                 ->assertSee($availableNovices[2]->name)
                 ->assertDontSee($unavailableNovices[0]->name)
                 ->assertDontSee($unavailableNovices[1]->name)
                 ->assertDontSee($unavailableNovices[2]->name);
    }

    /** @test */
    public function cannot_subscribe_novices_if_course_class_has_no_lessons()
    {
        $courseClass = CourseClass::factory()->create();

        $response = $this->actingAs($this->coordinator)
                         ->get(route('classes.subscriptions.create', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertOk()
                 ->assertSessionHas('no-lessons', 'Ainda não há aulas cadastradas para essa turma.')
                 ->assertSee('Ainda não há aulas cadastradas para essa turma.');
    }

    /** @test */
    public function cannot_subscribe_if_there_is_no_novices_available()
    {
        User::all()->each->delete();
        $courseClass = CourseClass::factory()->create();
        $courseClass->createLessonsFromArray($this->lessonsArray());

        $response = $this->actingAs($this->coordinator)
                         ->get(route('classes.subscriptions.create', [
                            'courseClass' => $courseClass
                        ]));

        $response->assertOk()
                 ->assertSessionHas('no-novices', 'Não há aprendizes disponíveis para matricular.')
                 ->assertSee('Não há aprendizes disponíveis para matricular.');
    }

    /** @test */
    public function guest_cannot_create_a_subscription()
    {
        $response = $this->get(route('classes.subscriptions.create', [
            'courseClass' => $this->courseClass,
        ]));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_create_a_subscription()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('classes.subscriptions.create', [
                            'courseClass' => $this->courseClass,
                        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_create_a_subscription()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('classes.subscriptions.create', [
                            'courseClass' => $this->courseClass,
                        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_create_a_subscription()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('classes.subscriptions.create', [
                            'courseClass' => $this->courseClass,
                        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_create_a_subscription()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('classes.subscriptions.create', [
                            'courseClass' => $this->courseClass,
                        ]));

        $response->assertUnauthorized();
    }
}
