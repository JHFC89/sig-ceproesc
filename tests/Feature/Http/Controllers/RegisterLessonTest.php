<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use Tests\Traits\LessonTestData;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterLessonTest extends TestCase
{
    use RefreshDatabase, LessonTestData;

    private $instructor;

    protected function setUp():void
    {
        parent::setUp();

        $this->instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
    }

    /** @test */
    public function a_lesson_can_be_registered_by_an_instructor_assigned_to_it()
    {
        $lesson = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(2)
            ->create(['instructor_id' => $this->instructor->id]);
        extract($lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');

        $data = $this->data()
                     ->change('register', 'Example lesson register')
                     ->change('presenceList', [
                        $novice_0->id => [
                             'presence'     => 1,
                             'observation'  => 'test observation for a novice',
                        ],
                        $novice_1->id => [
                             'presence' => 0,
                        ],
                     ])
                     ->get();
        
        $response = $this->actingAs($this->instructor, 'api')->postJson('api/lessons/register/' . $lesson->id, $data);

        $response->assertStatus(201);
        $this->assertEquals('Example lesson register', $lesson->fresh()->register);
        $this->assertNotNull($lesson->fresh()->registered_at);
        $this->assertTrue($lesson->isPresent($novice_0));
        $this->assertTrue($lesson->isAbsent($novice_1));
        $this->assertEquals('test observation for a novice', $lesson->observationFor($novice_0));
        $this->assertNull($lesson->observationFor($novice_1));
        $this->assertCount(2, $lesson->novices);
    }

    /** @test */
    public function a_guest_cannot_register_a_lesson()
    {
        $lesson = Lesson::factory()->forToday()->hasNovices(2)->create(['instructor_id' => $this->instructor->id]);

        $data = $this->data()->lesson($lesson)->get();
        
        $response = $this->postJson('api/lessons/register/' . $lesson->id, $data);

        $response->assertUnauthorized();
        $this->assertNull($lesson->fresh()->register);
    }

    /** @test */
    public function a_lesson_cannot_be_registered_by_an_user_thats_not_an_instructor()
    {
        $this->withoutExceptionHandling();
        $userNotInstructor = User::factory()->create();
        $lesson = Lesson::factory()->forToday()->hasNovices(2)->create(['instructor_id' => $this->instructor->id]);
        $data = $this->data()->lesson($lesson)->get();
        
        $response = $this
            ->actingAs($userNotInstructor, 'api')
            ->postJson('api/lessons/register/' . $lesson->id, $data);

        $response
            ->assertUnauthorized()
            ->assertJson(['error' => 'Action not authorized for this user']);
        $this->assertNull($lesson->fresh()->register);
    }

    /** @test */
    public function a_lesson_cannot_be_registered_by_an_instructor_not_assigned_to_it()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lessonForAnotherInstructor = Lesson::factory()->forToday()->hasNovices(2)->create(['instructor_id' => $this->instructor->id]);
        $data = $this->data()->lesson($lessonForAnotherInstructor)->get();
        
        $response = $this
            ->actingAs($instructor, 'api')
            ->postJson('api/lessons/register/' . $lessonForAnotherInstructor->id, $data);

        $response
            ->assertUnauthorized()
            ->assertJson(['error' => 'Action not authorized for this instructor']);
        $this->assertNull($lessonForAnotherInstructor->fresh()->register);
    }

    /** @test */
    public function a_registered_lesson_cannot_be_registered_again()
    {
        $lesson = Lesson::factory()->registered()->create(['instructor_id' => $this->instructor->id]);
        
        $response = $this->actingAs($this->instructor, 'api')
                         ->postJson(
                            'api/lessons/register/' . $lesson->id, 
                            $this->data()->change('register', 'Trying to register lesson again')->get()
                        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'error' => 'Lesson already registered',
            ]);
        $this->assertNotEquals('Trying to register lesson again', $lesson->fresh()->register);
    }

    /** @test */
    public function only_todays_lesson_can_be_registered()
    {
        $lesson = Lesson::factory()->notForToday()->create(['instructor_id' => $this->instructor->id]);

        $response = $this->actingAs($this->instructor, 'api')
                         ->postJson(
                            'api/lessons/register/' . $lesson->id,
                            $this->data()
                                 ->change('register', 'Lesson is not available to register at this date')
                                 ->get()
                         );

        $response
            ->assertStatus(422)
            ->assertJson([
                'error' => 'Lesson is not available to register at this date',
            ]);
        $this->assertNotEquals('Trying to register unavailable date lesson', $lesson->fresh()->register);
    }

    /** @test */
    public function registering_a_draft_lesson()
    {
        $lesson = Lesson::factory()->forToday()->hasNovices(1)->draft()->create(['instructor_id' => $this->instructor->id]);
        $novice = $lesson->novices->first();
        
        $response = $this->actingAs($this->instructor, 'api')
                         ->postJson('api/lessons/register/' . $lesson->id, [
                            'register' => 'Example draft lesson register',
                            'presenceList' => [
                                $novice->id => [
                                    'presence' => 1,
                                ],
                            ],
                        ]);

        $response->assertStatus(201);
        $this->assertEquals('Example draft lesson register', $lesson->fresh()->register);
        $this->assertNotNull($lesson->fresh()->registered_at);
    }

    /** @test */
    public function registering_a_draft_lesson_wont_duplicate_the_novices_presence()
    {
        $lesson = Lesson::factory()->forToday()->hasNovices(2)->draft()->create(['instructor_id' => $this->instructor->id]);
        extract($lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');
        $data = $this->data()
                     ->change('presenceList', [
                         $novice_0->id => [
                             'presence' => 0,
                         ],
                         $novice_1->id => [
                             'presence' => 0,
                         ],
                     ])
                     ->get();
        $this->actingAs($this->instructor, 'api')->postJson('api/lessons/draft/' . $lesson->id, $data);

        $data = $this->data()
                     ->change('presenceList', [
                         $novice_0->id => [
                             'presence' => 1,
                         ],
                         $novice_1->id => [
                             'presence' => 1,
                         ],
                     ])
                     ->get();
        $response = $this->actingAs($this->instructor, 'api')->postJson('api/lessons/register/' . $lesson->id, $data);

        $response->assertStatus(201);
        $this->assertCount(2, $lesson->fresh()->novices);
    }

    /** @test */
    public function register_field_is_required()
    {
        $lesson = Lesson::factory()->forToday()->create(['instructor_id' => $this->instructor->id]);
        
        $response = $this->actingAs($this->instructor, 'api')
                         ->postJson(
                            'api/lessons/register/' . $lesson->id,
                            $this->data()->exclude('register')->get()
                        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['register']);
    }

    /** @test */
    public function presence_list_field_is_required()
    {
        $lesson = Lesson::factory()->forToday()->create(['instructor_id' => $this->instructor->id]);
        
        $response = $this->actingAs($this->instructor, 'api')
                         ->postJson(
                            'api/lessons/register/' . $lesson->id,
                            $this->data()->exclude('presenceList')->get()
                        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['presenceList']);
    }

    /** @test */
    public function presence_key_in_the_presence_list_field_is_required()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lesson = Lesson::factory()->forToday()->hasNovices(2)->create(['instructor_id' => $this->instructor->id]);
        extract($lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');

        $response = $this->actingAs($this->instructor, 'api')->postJson(
            'api/lessons/register/' . $lesson->id,
            $this->data()
                 ->change('presenceList', [
                     $novice_0->id => [],
                     $novice_1->id => [],
                 ])
                 ->get()
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['presenceList.' . $novice_0->id . '.presence'])
            ->assertJsonValidationErrors(['presenceList.' . $novice_1->id . '.presence']);
    }

    /** @test */
    public function presence_key_in_the_presence_list_field_must_be_boolean()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lesson = Lesson::factory()->forToday()->hasNovices(2)->create(['instructor_id' => $this->instructor->id]);
        extract($lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');

        $response = $this->actingAs($this->instructor, 'api')->postJson(
            'api/lessons/register/' . $lesson->id,
            $this->data()
                 ->change('presenceList', [
                     $novice_0->id => [
                         'presence' => 2,
                     ],
                     $novice_1->id => [
                         'presence' => 'was present',
                     ],
                 ])
                 ->get()
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['presenceList.' . $novice_0->id . '.presence'])
            ->assertJsonValidationErrors(['presenceList.' . $novice_1->id . '.presence']);
    }

    /** @test */
    public function observation_key_in_the_presence_list_field_must_be_string()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lesson = Lesson::factory()->forToday()->hasNovices(2)->create(['instructor_id' => $this->instructor->id]);
        extract($lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');

        $response = $this->actingAs($this->instructor, 'api')->postJson(
            'api/lessons/register/' . $lesson->id,
            $this->data()
                 ->change('presenceList', [
                     $novice_0->id => [
                         'presence' => 1,
                         'observation' => 1,
                     ],
                     $novice_1->id => [
                         'presence' => 0,
                         'observation' => false,
                     ],
                 ])
                 ->get()
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['presenceList.' . $novice_0->id . '.observation'])
            ->assertJsonValidationErrors(['presenceList.' . $novice_1->id . '.observation']);
    }
}
