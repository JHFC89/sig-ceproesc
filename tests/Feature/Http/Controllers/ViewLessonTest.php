<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Discipline;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\Lesson;
use App\Models\CourseClass;
use App\Models\LessonRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewLessonTest extends TestCase
{
    use RefreshDatabase;

    private $instructor;

    private $coordinator;

    private $courseClass;

    private $notRegisteredLesson;

    protected function setUp():void
    {
        parent::setUp();

        $this->instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $this->coordinator = User::factory()->hasRoles(1, ['name' => 'coordinator'])->create();

        $this->courseClass = CourseClass::factory()->create(['name' => '2020 - julho']);

        $this->notRegisteredLesson = Lesson::factory()->notRegistered()->forToday()->instructor($this->instructor)->hasNovices(3)->create();

        $this->novices = $this->notRegisteredLesson->novices->each(function ($novice) {
            $novice->turnIntoNovice();
            $this->courseClass->subscribe($novice);
        });
    }

    /** @test */
    public function instructor_can_view_a_not_registered_lesson_he_is_assigned_to()
    {
        $date = Carbon::now();
        $discipline = Discipline::factory()->create([
            'name' => 'administração'
        ]);
        $lesson = Lesson::factory()
            ->notRegistered()
            ->instructor($this->instructor)
            ->hasNovices(3)
            ->create([
                'date'          => $date,
                'discipline_id' => $discipline,
                'hourly_load'   => '123hr',
        ]);
        $lesson->novices->each(function ($novice) {
            $novice->turnIntoNovice();
            $this->courseClass->subscribe($novice);
        });
        extract($lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');

        $response = $this->actingAs($this->instructor)->get(route('lessons.show', ['lesson' => $lesson]));

        $response
            ->assertOk()
            ->assertSee($this->instructor->name)
            ->assertSee($date->format('d/m/Y'))
            ->assertSee('administração')
            ->assertSee('123hr')
            ->assertDontSee('"presença"')
            ->assertDontSee('"observação"')
            ->assertDontSee('Nenhuma observação registrada')
            ->assertSee($novice_0->name)
            ->assertSee($novice_0->code)
            ->assertSee($novice_0->class)
            ->assertSee($novice_1->name)
            ->assertSee($novice_1->code)
            ->assertSee($novice_1->class)
            ->assertSee($novice_2->name)
            ->assertSee($novice_2->code)
            ->assertSee($novice_2->class)
            ->assertDontSee(route('lessons.requests.create', ['lesson' => $lesson]));
    }

    /** @test */
    public function instructor_can_view_a_registered_lesson_he_is_assigned_to()
    {
        $this->notRegisteredLesson
             ->registerFor($this->novices[0])
             ->present()
             ->observation('Test observation for novice_0')
             ->complete();
        $this->notRegisteredLesson
             ->registerFor($this->novices[1])
             ->absent()
             ->observation('Test observation for novice_1')
             ->complete();
        $this->notRegisteredLesson
             ->registerFor($this->novices[2])
             ->present()
             ->complete();
        $this->notRegisteredLesson->registered_at = now();
        $this->notRegisteredLesson->save();

        $response = $this->actingAs($this->instructor)->get(route('lessons.show', ['lesson' => $this->notRegisteredLesson]));

        $response
            ->assertOk()
            ->assertSee($this->instructor->name)
            ->assertSee($this->notRegisteredLesson->formatted_date)
            ->assertSee('presença')
            ->assertSee('observação')
            ->assertSee('Test observation for novice_0')
            ->assertSee('Test observation for novice_1')
            ->assertSee('Nenhuma observação registrada')
            ->assertSee(route('lessons.requests.create', ['lesson' => $this->notRegisteredLesson]));
    }

    /** @test */
    public function instructor_cannot_view_a_lesson_he_is_not_assigned_to()
    {
        $lessonForAnotherInstructor = Lesson::factory()->notRegistered()->hasNovices(3)->create();
        
        $response = $this->actingAs($this->instructor)->get(route('lessons.show', ['lesson' => $lessonForAnotherInstructor]));

        $response->assertNotFound();
    }

    /** @test */
    public function instructor_can_view_a_link_to_register_a_lesson_when_it_is_available_to_register()
    {
        $lesson = Lesson::factory()->forToday()->notRegistered()->instructor($this->instructor)->create();
        
        $response = $this->actingAs($this->instructor)->get(route('lessons.show', ['lesson' => $lesson]));

        $response
            ->assertOk()
            ->assertSee('registrar')
            ->assertSee(route('lessons.registers.create', ['lesson' => $lesson]));
    }

    /** @test */
    public function only_instructor_can_view_a_link_to_register_a_lesson_when_it_is_available_to_register()
    {
        $lesson = Lesson::factory()->forToday()
                                   ->notRegistered()
                                   ->hasNovices(1)
                                   ->create();
        $lesson->setTestData();
        $novice = $lesson->novices->first();
        $employer = User::fakeEmployer();
        $employer->company->novices()->save($novice->registration);
        
        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', ['lesson' => $lesson]));

        $coordinatorResponse = $this->actingAs($this->coordinator)
                                    ->get(route('lessons.show', [
                                        'lesson' => $lesson
                                    ]));
        $noviceResponse = $this->actingAs($novice)
                               ->get(route('lessons.show', [
                                   'lesson' => $lesson
                               ]));
        $employerResponse = $this->actingAs($employer)
                                 ->get(route('lessons.show', [
                                     'lesson' => $lesson
                                 ]));

        $coordinatorResponse
            ->assertOk()
            ->assertDontSee(route('lessons.registers.create', [
                'lesson' => $lesson
            ]));
        $noviceResponse
            ->assertOk()
            ->assertDontSee(route('lessons.registers.create', [
                'lesson' => $lesson
            ]));
        $employerResponse
            ->assertOk()
            ->assertDontSee(route('lessons.registers.create', [
                'lesson' => $lesson
            ]));
    }

    /** @test */
    public function instructor_can_see_a_link_to_create_an_evaluation_for_the_lesson_that_does_not_have_one()
    {
        $lesson = Lesson::factory()->instructor($this->instructor)
                                   ->hasNovices(3)
                                   ->create();

        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', ['lesson' => $lesson]));

        $response
            ->assertOk()
            ->assertSee(route('lessons.evaluations.create', [
                'lesson' => $lesson
            ]));
    }

    /** @test */
    public function instructor_can_see_a_link_to_view_an_evaluation_for_the_lesson_that_already_have_one()
    {
        $lesson = Lesson::factory()->hasEvaluation(1)
                                   ->instructor($this->instructor)
                                   ->hasNovices(3)
                                   ->create();
        $evaluation = $lesson->evaluation;

        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', ['lesson' => $lesson]));

        $response
            ->assertOk()
            ->assertSee(route('evaluations.show', [
                'evaluation' => $evaluation
            ]))
            ->assertDontSee(route('lessons.evaluations.create', [
                'lesson' => $lesson
            ]));
    }

    /** @test */
    public function only_instructor_can_see_the_link_to_create_an_evaluation()
    {
        $lesson = Lesson::factory()->instructor($this->instructor)
                                   ->hasNovices(3)
                                   ->create();
        $lesson->setTestData();
        $novice = $lesson->novices->first();
        $employer = User::fakeEmployer();
        $employer->company->novices()->save($novice->registration);


        $coordinatorResponse = $this->actingAs($this->coordinator)
                                    ->get(route('lessons.show', [
                                        'lesson' => $lesson
                                    ]));
        $noviceResponse = $this->actingAs($novice)
                               ->get(route('lessons.show', [
                                   'lesson' => $lesson
                               ]));
        $employerResponse = $this->actingAs($employer)
                                 ->get(route('lessons.show', [
                                     'lesson' => $lesson
                                 ]));

        $coordinatorResponse
            ->assertOk()
            ->assertDontSee(route('lessons.evaluations.create', [
                'lesson' => $lesson
            ]));
        $noviceResponse
            ->assertOk()
            ->assertDontSee(route('lessons.evaluations.create', [
                'lesson' => $lesson
            ]));
        $employerResponse
            ->assertOk()
            ->assertDontSee(route('lessons.evaluations.create', [
                'lesson' => $lesson
            ]));
    }

    /** @test */
    public function instructor_should_see_a_warning_when_a_lesson_register_deadline_is_expired()
    {
        $this->travel(25)->hours();

        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertSee('Prazo para registro dessa aula vencido');
    }

    /** @test */
    public function instructor_should_not_see_a_warning_when_a_lesson_register_deadline_is_not_expired()
    {
        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertDontSee('Prazo para registro dessa aula vencido');
    }

    /** @test */
    public function instructor_should_see_a_warning_when_an_expired_lesson_has_an_open_request_to_register()
    {
        $this->travel(25)->hours();
        LessonRequest::for($this->notRegisteredLesson, 'Fake Justification');

        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertSee('Aula com pedido de liberação para registro em aberto')
            ->assertDontSee('Prazo para registro dessa aula vencido');
    }

    /** @test */
    public function instructor_should_see_a_warning_when_a_registered_lesson_has_an_open_request_to_rectify()
    {
        $registeredLesson = Lesson::factory()->registered()
                                             ->instructor($this->instructor)
                                             ->hasRequests(1)
                                             ->hasNovices(3)
                                             ->create();

        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $registeredLesson
                         ]));

        $response
            ->assertOk()
            ->assertSee('Aula com pedido de retificação em aberto');
    }

    /** @test */
    public function instructor_can_see_a_link_to_request_permission_to_register_a_expired_lesson()
    {
        $this->travel(25)->hours();

        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertSee('Solicitar liberação da aula')
            ->assertSee(route('lessons.requests.create', [
                'lesson' => $this->notRegisteredLesson
            ]))
            ->assertDontSee(route('lessons.registers.create', [
                'lesson' => $this->notRegisteredLesson
            ]));
    }

    /** @test */
    public function instructor_cannot_see_a_link_to_request_permission_to_register_a_not_expired_lesson()
    {
        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertDontSee('Solicitar liberação da aula')
            ->assertDontSee(route('lessons.requests.create', [
                'lesson' => $this->notRegisteredLesson
            ]));
    }

    /** @test */
    public function instructor_can_see_a_link_to_view_an_open_request_for_the_lesson()
    {
        $this->travel(25)->hours();
        $request = LessonRequest::for(
            $this->notRegisteredLesson,
            'Fake Justification'
        );

        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertSee('Ver solicitação')
            ->assertSee(route('requests.show', ['request' => $request]));
    }

    /** @test */
    public function instructor_should_see_a_warning_that_an_expired_lesson_is_released_to_register()
    {
        $this->travel(25)->hours();
        $request = LessonRequest::for(
            $this->notRegisteredLesson,
            'Fake Justification'
        );
        $request->release();

        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertSee('Aula vencida liberada para registro');
    }

    /** @test */
    public function instructor_should_see_a_warning_that_a_rectification_is_released_to_register()
    {
        $lesson = Lesson::factory()->registered()
                                   ->instructor($this->instructor)
                                   ->hasRequests(1)
                                   ->create();
        $request = LessonRequest::for($lesson, 'Fake Justification');
        $request->release();

        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $lesson
                         ]));

        $response
            ->assertOk()
            ->assertSee('Aula liberada para retificação');
    }

    /** @test */
    public function instructor_should_see_a_link_to_register_an_expired_lesson_released_to_register()
    {
        $this->travel(25)->hours();
        $request = LessonRequest::for(
            $this->notRegisteredLesson,
            'Fake Justification'
        );
        $request->release();

        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertSee('Registrar')
            ->assertSee(route('lessons.registers.create', [
                'lesson' => $this->notRegisteredLesson
            ]));
    }

    /** @test */
    public function instructor_should_see_a_link_to_rectify_a_registered_lesson_released_to_register()
    {
        $lesson = Lesson::factory()->registered()
                                   ->instructor($this->instructor)
                                   ->hasRequests(1)
                                   ->create();
        $lesson->openRequest()->release();

        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $lesson
                         ]));

        $response
            ->assertOk()
            ->assertSee('Registrar')
            ->assertSee(route('lessons.registers.create', [
                'lesson' => $lesson
            ]));
    }

    /** @test */
    public function instructor_is_the_only_who_can_see_the_rectification_button()
    {
        $lesson = Lesson::factory()->registered()
                                   ->instructor($this->instructor)
                                   ->hasNovices(1)
                                   ->create();
        $lesson->setTestData();
        $employer = User::fakeEmployer();
        $employer->company->novices()->save($lesson->novices->first()->registration);
        
        $responseForInstructor = $this->actingAs($this->instructor)
                                      ->get(route('lessons.show', [
                                          'lesson' => $lesson
                                      ]));
        $responseForNovice = $this->actingAs($lesson->novices->first())
                                  ->get(route('lessons.show', [
                                      'lesson' => $lesson
                                  ]));
        $responseForEmployer = $this->actingAs($employer)
                                    ->get(route('lessons.show', [
                                        'lesson' => $lesson
                                    ]));
        $responseForCoordinator = $this->actingAs($this->coordinator)
                                       ->get(route('lessons.show', [
                                           'lesson' => $lesson
                                       ]));

        $responseForInstructor
            ->assertOk()
            ->assertSee(route('lessons.requests.create', [
                'lesson' => $lesson
            ]));

        $responseForNovice
            ->assertOk()
            ->assertDontSee(route('lessons.requests.create', [
                'lesson' => $lesson
            ]));

        $responseForEmployer
            ->assertOk()
            ->assertDontSee(route('lessons.requests.create', [
                'lesson' => $lesson]
            ));

        $responseForCoordinator
            ->assertOk()
            ->assertDontSee(route('lessons.requests.create', [
                'lesson' => $lesson
            ]));
    }

    /** @test */
    public function instructor_cannot_see_the_rectification_button_when_there_is_an_open_request()
    {
        $lesson = Lesson::factory()->registered()
                                   ->hasRequests(1)
                                   ->instructor($this->instructor)
                                   ->hasNovices(1)
                                   ->create();
        $lesson->setTestData();
        
        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $lesson
                         ]));

        $response
            ->assertOk()
            ->assertDontSee(route('lessons.requests.create', [
                'lesson' => $lesson
            ]));
    }

    /** @test */
    public function instructor_cannot_see_the_rectification_button_when_there_is_a_pending_request()
    {
        $lesson = Lesson::factory()->registered()
                                   ->hasRequests(1)
                                   ->instructor($this->instructor)
                                   ->hasNovices(1)
                                   ->create();
        $lesson->setTestData();
        $lesson->openRequest()->release();
        
        $response = $this->actingAs($this->instructor)
                         ->get(route('lessons.show', [
                             'lesson' => $lesson
                         ]));

        $response
            ->assertOk()
            ->assertDontSee(route('lessons.requests.create', [
                'lesson' => $lesson
            ]));
    }

    /** @test */
    public function coordinator_can_view_the_list_of_novices_for_a_lesson()
    {
        $lesson = Lesson::factory()->hasNovices(3)->create();
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)
                         ->get(route('lessons.show', [
                             'lesson' => $lesson
                         ]));

        $response
            ->assertOk()
            ->assertSee($lesson->novices[0]->code)
            ->assertSee($lesson->novices[0]->name)
            ->assertSee($lesson->novices[1]->code)
            ->assertSee($lesson->novices[1]->name)
            ->assertSee($lesson->novices[2]->code)
            ->assertSee($lesson->novices[2]->name);
    }

    /** @test */
    public function coordinator_should_see_a_warning_and_link_to_view_an_open_request_to_register()
    {
        $this->travel(25)->hours();
        $request = LessonRequest::for(
            $this->notRegisteredLesson,
            'Fake Justification'
        );

        $response = $this->actingAs($this->coordinator)
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertSee('Aula com pedido de liberação para registro em aberto')
            ->assertSee('Ver solicitação')
            ->assertSee(route('requests.show', ['request' => $request]));
    }

    /** @test */
    public function coordinator_should_see_a_warning_that_an_expired_lesson_has_a_pending_request()
    {
        $this->travel(25)->hours();
        $request = LessonRequest::for(
            $this->notRegisteredLesson,
            'Fake Justification'
        );
        $request->release();

        $response = $this->actingAs($this->coordinator)
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertSee('Aula vencida liberada para registro');
    }

    /** @test */
    public function coordinator_cannot_see_a_link_to_register_an_expired_lesson_released_to_register()
    {
        $this->travel(25)->hours();
        $request = LessonRequest::for(
            $this->notRegisteredLesson, 'Fake Justification'
        );
        $request->release();

        $response = $this->actingAs($this->coordinator)
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertDontSee('Registrar')
            ->assertDontSee(route('lessons.registers.create', [
                'lesson' => $this->notRegisteredLesson
            ]));
    }

    /** @test */
    public function guest_cannot_view_any_lesson()
    {
        $lesson = Lesson::factory()->hasNovices(2)->create();

        $response = $this->get(route('lessons.show', ['lesson' => $lesson]));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_with_no_permissions_cannot_view_any_lesson()
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('lessons.show', ['lesson' => $lesson]));

        $response->assertNotFound();
    }

    /** @test */
    public function novice_can_view_a_not_registered_lesson_he_is_enrolled()
    {
        $courseClass = CourseClass::factory()->create();
        $lesson = Lesson::factory()->hasNovices(1)->notRegistered()->create();
        $novice = $lesson->novices->first();
        $novice->turnIntoNovice();
        $courseClass->subscribe($novice);
        
        $response = $this->actingAs($novice)
                         ->get(route('lessons.show', ['lesson' => $lesson]));

        $response
            ->assertOk()
            ->assertSee($novice->name)
            ->assertSee($lesson->instructor->name)
            ->assertSee($lesson->formatted_date)
            ->assertDontSee('observação')
            ->assertDontSee('presença')
            ->assertDontSee('label="registro"');
    }

    /** @test */
    public function novice_can_view_a_lesson_he_is_enrolled_to()
    {
        $lesson = Lesson::factory()->hasNovices(1)->notRegistered()->create();
        $novice = $lesson->novices->first();
        $novice->turnIntoNovice();
        $courseClass = CourseClass::factory()->create();
        $courseClass->subscribe($novice);
        $lesson->registerFor($novice)
               ->present()
               ->observation('Test Novice observation')
               ->complete();
        $lesson->registered_at = now();
        $lesson->save();

        $response = $this->actingAs($novice)
                         ->get(route('lessons.show', ['lesson' => $lesson]));

        $response
            ->assertOk()
            ->assertSee($novice->name)
            ->assertSee('Test Novice observation')
            ->assertSee('presente');
    }

    /** @test */
    public function novice_cannot_view_a_lesson_he_is_not_enrolled_to()
    {
        $lessonForAnotherNovice = Lesson::factory()->hasNovices(3)->create();
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();

        $response = $this->actingAs($novice)
                         ->get(route('lessons.show', [
                             'lesson' => $lessonForAnotherNovice
                         ]));

        $response->assertNotFound();
    }

    /** @test */
    public function novice_cannot_view_another_novice_informations_for_the_same_lesson()
    {
        $lesson = Lesson::factory()->hasNovices(2)->notRegistered()->create();
        $noviceA = $lesson->novices->first();
        $noviceA->turnIntoNovice();
        $noviceB = $lesson->novices->last();
        $noviceB->turnIntoNovice();
        $courseClass = CourseClass::factory()->create();
        $courseClass->subscribe($noviceA);
        $courseClass->subscribe($noviceB);
        $lesson->registerFor($noviceA)
               ->present()
               ->observation('Test Novice A observation')
               ->complete();
        $lesson->registerFor($noviceB)
               ->absent()
               ->observation('Test Novice B observation')
               ->complete();
        $lesson->registered_at = now();
        $lesson->save();

        $response = $this->actingAs($noviceA)
                         ->get(route('lessons.show', ['lesson' => $lesson]));

        $response
            ->assertOk()
            ->assertSee($noviceA->name)
            ->assertSee('Test Novice A observation')
            ->assertSee('presente')
            ->assertDontSee($noviceB->name)
            ->assertDontSee('Test Novice B observation')
            ->assertDontSee('ausente');
    }

    /** @test */
    public function novice_cannot_view_warning_and_link_to_request_to_expired_lesson()
    {
        $this->travel(25)->hours();

        $response = $this->actingAs($this->novices->first())
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertDontSee('Prazo para registro dessa aula vencido')
            ->assertDontSee('Solicitar liberação da aula')
            ->assertDontSee(route('lessons.requests.create', [
                'lesson' => $this->notRegisteredLesson
            ]));
    }

    /** @test */
    public function novice_cannot_view_warning_and_link_to_view_an_open_request()
    {
        $this->travel(25)->hours();
        $request = LessonRequest::for(
            $this->notRegisteredLesson,
            'Fake Justification'
        );

        $response = $this->actingAs($this->novices->first())
                         ->get(route('lessons.show', [
                             'lesson' => $this->notRegisteredLesson
                         ]));

        $response
            ->assertOk()
            ->assertDontSee('Aula com pedido de liberação para registro em aberto')
            ->assertDontSee('Ver solicitação')
            ->assertDontSee(route('requests.show', ['request' => $request]));
    }

    /** @test */
    public function employer_can_view_his_novices_informations_for_a_not_registered_lesson_they_are_enrolled()
    {
        $courseClass = CourseClass::factory()->create();
        $novices = User::factory()->hasRoles(1, ['name' => 'novice'])
                                  ->count(3)
                                  ->create();
        $employer = User::fakeEmployer();
        $employer->company->novices()->saveMany($novices->map->registration->all());
        $lesson = Lesson::factory()->notRegistered()->create();
        $novices->each(function ($novice) use ($lesson, $courseClass) {
            $courseClass->subscribe($novice);
            $lesson->enroll($novice);
        });
        
        $response = $this->actingAs($employer)
                         ->get(route('lessons.show', ['lesson' => $lesson]));

        $response
            ->assertOk()
            ->assertSee($novices[0]->code)
            ->assertSee($novices[0]->name)
            ->assertSee($novices[1]->code)
            ->assertSee($novices[1]->name)
            ->assertSee($novices[2]->code)
            ->assertSee($novices[2]->name);
    }

    /** @test */
    public function employer_can_view_his_novices_informations_for_a_registered_lesson_they_are_enrolled()
    {
        $courseClass = CourseClass::factory()->create();
        $novices = User::factory()->hasRoles(1, ['name' => 'novice'])
                                  ->count(3)
                                  ->create();
        $employer = User::fakeEmployer();
        $employer->company->novices()->saveMany($novices->map->registration->all());
        $lesson = Lesson::factory()->notRegistered()->create();
        $novices->each(function ($novice) use ($lesson, $courseClass) {
            $lesson->enroll($novice);
            $courseClass->subscribe($novice);
        });
        $lesson->registerFor($novices[0])
               ->present()
               ->observation('Test observation for first novice')
               ->complete();
        $lesson->registerFor($novices[1])
               ->absent()
               ->observation('Test observation for second novice')
               ->complete();
        $lesson->registerFor($novices[2])->present()->complete();
        $lesson->registered_at = now();
        $lesson->save();
        
        $response = $this->actingAs($employer)
                         ->get(route('lessons.show', ['lesson' => $lesson]));

        $response
            ->assertOk()
            ->assertSee($novices[0]->code)
            ->assertSee($novices[0]->name)
            ->assertSee('Test observation for first novice')
            ->assertSee('Test observation for second novice')
            ->assertSee('Nenhuma observação registrada')
            ->assertSee($novices[1]->code)
            ->assertSee($novices[1]->name)
            ->assertSee($novices[2]->code)
            ->assertSee($novices[2]->name);
    }

    /** @test */
    public function employer_cannot_view_another_employer_novices_informations_for_a_lesson_he_has_novices_enrolled()
    {
        $courseClass = CourseClass::factory()->create();
        $novicesForEmployerA = User::factory()->hasRoles(1, ['name' => 'novice'])
                                              ->count(3)
                                              ->create();
        $employerA = User::fakeEmployer();
        $employerA->company->novices()->saveMany($novicesForEmployerA->map->registration->all());
        $novicesForEmployerB = User::factory()->hasRoles(1, ['name' => 'novice'])
                                              ->count(3)
                                              ->create();
        $employerB = User::fakeEmployer();
        $employerB->company->novices()->saveMany($novicesForEmployerB->map->registration->all());
        $lesson = Lesson::factory()->notRegistered()->create();
        collect([$novicesForEmployerA, $novicesForEmployerB])->flatten()->each(function ($novice) use ($lesson, $courseClass) {
            $courseClass->subscribe($novice);
            $lesson->enroll($novice);
        });
        
        $response = $this->actingAs($employerA)
                         ->get(route('lessons.show', ['lesson' => $lesson]));

        $response
            ->assertOk()
            ->assertDontSee($novicesForEmployerB[0]->code)
            ->assertDontSee($novicesForEmployerB[0]->name)
            ->assertDontSee($novicesForEmployerB[1]->code)
            ->assertDontSee($novicesForEmployerB[1]->name)
            ->assertDontSee($novicesForEmployerB[2]->code)
            ->assertDontSee($novicesForEmployerB[2]->name);
    }

    /** @test */
    public function employer_cannot_view_a_lesson_he_does_not_have_any_novice_enrolled()
    {
        $employer = User::fakeEmployer();
        $lesson = Lesson::factory()->hasNovices(3)->notRegistered()->create();

        $response = $this->actingAs($employer)
                         ->get(route('lessons.show', ['lesson' => $lesson]));

        $response->assertNotFound();
    }

    /** @test */
    public function employer_cannot_view_warning_and_link_to_view_an_open_request()
    {
        $courseClass = CourseClass::factory()->create();
        $novices = User::factory()->hasRoles(1, ['name' => 'novice'])
                                  ->count(3)
                                  ->create();
        $employer = User::fakeEmployer();
        $employer->company->novices()->saveMany($novices->map->registration->all());
        $lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $novices->each(function ($novice) use ($lesson, $courseClass) {
            $courseClass->subscribe($novice);
            $lesson->enroll($novice);
        });
        $request = LessonRequest::for($lesson, 'Fake Justification');

        $response = $this->actingAs($employer)
                         ->get(route('lessons.show', ['lesson' => $lesson]));

        $response
            ->assertOk()
            ->assertDontSee('Aula com pedido de liberação para registro em aberto')
            ->assertDontSee('Ver solicitação')
            ->assertDontSee(route('requests.show', ['request' => $request]));
    }
}
