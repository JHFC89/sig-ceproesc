<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Discipline;
use Tests\TestCase;
use App\Models\User;
use App\Models\CourseClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected $courseClass;

    protected $coordinator;

    protected $novice;

    protected function setUp():void
    {
        parent::setUp();

        $this->courseClass = CourseClass::factory()->create();
        $this->courseClass->createLessonsFromArray($this->lessonsArray());
        $this->courseClass->refresh();

        $this->coordinator = User::fakeCoordinator();

        $this->novice = User::fakeNovice();
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

    private function validData()
    {
        $noviceA = User::fakeNovice();
        $noviceB = User::fakeNovice();
        return [
            'class'     => $this->courseClass->id,
            'novices'   => [
                ['id' => $noviceA->id],
                ['id' => $noviceB->id],
            ],
        ];
    }

    /** @test */
    public function coordinator_can_subscribe_a_novice_to_a_course_class()
    {
        $courseClass = CourseClass::factory()->create();
        $courseClass->createLessonsFromArray($this->lessonsArray());
        $this->assertGreaterThan(0, $courseClass->fresh()->lessons->count());
        $novice = User::fakeNovice();
        $data = [
            'class'     => $courseClass->id,
            'novices'   => [
                ['id' => $novice->id],
            ],
        ];

        $response = $this->actingAs($this->coordinator)
                         ->post(route('subscriptions.store'), $data);

        $response->assertOk()
                 ->assertViewIs('subscriptions.create')
                 ->assertSessionHas('status', 'Aprendizes matriculados com sucesso!');

        $courseClass->refresh();
        $novice->refresh();
        $this->assertTrue($courseClass->isSubscribed($novice));
        $this->assertGreaterThan(0, $novice->lessons->count());
        $this->assertTrue($courseClass->lessons
                                      ->first()
                                      ->is($novice->lessons->first()));
        $this->assertTrue($courseClass->lessons
                                      ->last()
                                      ->is($novice->lessons->last()));
    }

    /** @test */
    public function cannot_subscribe_an_already_subscribed_novice()
    {
        $noviceA = User::fakeNovice();
        $noviceB = User::fakeNovice();
        $anotherCourseClass = CourseClass::factory()->create();
        $anotherCourseClass->createLessonsFromArray($this->lessonsArray());
        $anotherCourseClass->subscribe($noviceA);
        $anotherCourseClass->subscribe($noviceB);
        $this->assertTrue($anotherCourseClass->isSubscribed($noviceA));
        $this->assertTrue($anotherCourseClass->isSubscribed($noviceB));
        $data = [
            'class'     => $this->courseClass->id,
            'novices'   => [
                ['id' => $noviceA->id],
                ['id' => $noviceB->id],
            ],
        ];

        $response = $this->actingAs($this->coordinator)
                         ->post(route('subscriptions.store'), $data);

        $response->assertOk()
                 ->assertViewIs('subscriptions.create')
                 ->assertSessionHas('error', "Aprendizes já matriculados em outra turma: {$noviceA->name}, {$noviceB->name}.");

        $noviceA->refresh();
        $noviceB->refresh();
        $this->courseClass->refresh();
        $this->assertTrue($anotherCourseClass->isSubscribed($noviceA));
        $this->assertTrue($anotherCourseClass->isSubscribed($noviceB));
        $this->assertFalse($this->courseClass->isSubscribed($noviceA));
        $this->assertFalse($this->courseClass->isSubscribed($noviceB));
        $this->assertCount(0, $noviceA->lessons);
        $this->assertCount(0, $noviceB->lessons);
    }

    /** @test */
    public function can_subscribe_more_than_one_novice_to_a_course_class_at_a_time()
    {
        $noviceA = User::fakeNovice();
        $noviceB = User::fakeNovice();
        $data = [
            'class'     => $this->courseClass->id,
            'novices'   => [
                ['id' => $noviceA->id],
                ['id' => $noviceB->id],
            ],
        ];

        $response = $this->actingAs($this->coordinator)
                         ->post(route('subscriptions.store'), $data);

        $response->assertOk()
                 ->assertViewIs('subscriptions.create');

        $this->courseClass->refresh();
        $noviceA->refresh();
        $noviceB->refresh();
        $this->assertTrue($this->courseClass->isSubscribed($noviceA));
        $this->assertTrue($this->courseClass->isSubscribed($noviceB));
        $this->assertGreaterThan(0, $noviceA->lessons->count());
        $this->assertGreaterThan(0, $noviceB->lessons->count());
    }

    /** @test */
    public function guest_cannot_store_a_subscription()
    {
        $data = $this->validData();

        $response = $this->post(route('subscriptions.store'), $data);

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_store_a_subscription()
    {
        $data = $this->validData();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->post(route('subscriptions.store'), $data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_store_a_subscription()
    {
        $data = $this->validData();
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->post(route('subscriptions.store'), $data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_store_a_subscription()
    {
        $data = $this->validData();
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->post(route('subscriptions.store'), $data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_store_a_subscription()
    {
        $data = $this->validData();
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->post(route('subscriptions.store'), $data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function course_class_is_required()
    {
        $data = $this->validData();
        unset($data['class']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('subscriptions.store'), $data);

        $response->assertSessionHasErrors('class');
    }

    /** @test */
    public function course_class_must_exist()
    {
        $data = $this->validData();
        $data['class'] = 123;
        $this->assertNull(CourseClass::find(123));

        $response = $this->actingAs($this->coordinator)
                         ->post(route('subscriptions.store'), $data);

        $response->assertSessionHasErrors('class');
    }

    /** @test */
    public function novices_are_required()
    {
        $data = $this->validdata();
        unset($data['novices']);

        $response = $this->actingas($this->coordinator)
                         ->post(route('subscriptions.store'), $data);

        $response->assertsessionhaserrors('novices');
    }
}
