<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use Tests\Traits\LessonTestData;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterLessonTest extends TestCase
{
    use RefreshDatabase, LessonTestData;

    /** @test */
    public function can_be_registered()
    {
        $noviceA = User::factory()->create();
        $noviceB = User::factory()->create();
        $lesson = Lesson::factory()->forToday()->create();
        $lesson->enroll($noviceA);
        $lesson->enroll($noviceB);

        $data = $this->data()
                     ->change('register', 'Example lesson register')
                     ->change('presenceList', [
                        $noviceA->id => 3,
                        $noviceB->id => 2,
                     ])
                     ->get();
        
        $response = $this->postJson('api/lessons/register/' . $lesson->id, $data);

        $response->assertStatus(201);
        $this->assertEquals('Example lesson register', $lesson->fresh()->register);
        $this->assertNotNull($lesson->fresh()->registered_at);
        $this->assertEquals(3, $noviceA->lessons->firstWhere('id', $lesson->id)->presence->frequency);
        $this->assertEquals(2, $noviceB->lessons->firstWhere('id', $lesson->id)->presence->frequency);
        $this->assertCount(2, $lesson->novices);
    }

    /** @test */
    public function a_registered_lesson_cannot_be_registered_again()
    {
        $lesson = Lesson::factory()->registered()->create();
        
        $response = $this->postJson(
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
        $lesson = Lesson::factory()->notForToday()->create();

        $response = $this->postJson(
            'api/lessons/register/' . $lesson->id,
            $this->data()->change('register', 'Lesson is not available to register at this date')->get()
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'error' => 'Lesson is not available to register at this date',
            ]);
        $this->assertNotEquals('Trying to register unavailable date lesson', $lesson->fresh()->register);
    }

    /** @test */
    public function register_field_is_required()
    {
        $lesson = Lesson::factory()->forToday()->create();
        
        $response = $this->postJson('api/lessons/register/' . $lesson->id,
            $this->data()->exclude('register')->get()
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['register']);
    }

    /** @test */
    public function presence_list_field_is_required()
    {
        $lesson = Lesson::factory()->forToday()->create();
        
        $response = $this->postJson('api/lessons/register/' . $lesson->id,
            $this->data()->exclude('presenceList')->get()
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['presenceList']);
    }

    /** @test */
    public function a_user_can_view_a_lesson_available_to_registration_at_current_date()
    {
        $lesson = Lesson::factory()->forToday()->create([]);
        $noviceA = User::factory()->create();
        $noviceB = User::factory()->create();
        $noviceC = User::factory()->create();
        collect([$noviceA, $noviceB, $noviceC])->each(function ($novice) use ($lesson) {
            $lesson->enroll($novice);
        });

        $reponse = $this->get('lessons/register/create/' . $lesson->id);

        $reponse
            ->assertOk()
            ->assertViewHas('lesson', $lesson)
            ->assertSee($lesson->class)
            ->assertSee($lesson->discipline)
            ->assertSee($lesson->hourly_load)
            ->assertSee($noviceA->name)
            ->assertSee($noviceB->name)
            ->assertSee($noviceC->name);
    }

    /** @test */
    public function a_user_cannot_view_a_lesson_already_registered_even_if_its_available_for_current_date()
    {
        $lesson = Lesson::factory()->forToday()->registered()->create([]);

        $response = $this->get('lessons/register/create/' . $lesson->id);

        $response->assertStatus(404);
    }

    /** @test */
    public function a_register_can_be_saved_as_draft()
    {
        $noviceA = User::factory()->create();
        $noviceB = User::factory()->create();
        $lesson = Lesson::factory()->forToday()->create();
        $lesson->enroll($noviceA);
        $lesson->enroll($noviceB);
        $data = $this->data()
                     ->change('register', 'Example lesson register draft')
                     ->change('presenceList', [
                        $noviceA->id => 3,
                        $noviceB->id => 2,
                     ])
                     ->get();
        
        $response = $this->postJson('api/lessons/draft/' . $lesson->id, $data);

        $response->assertStatus(201);
        $this->assertEquals('Example lesson register draft', $lesson->fresh()->register);
        $this->assertNull($lesson->fresh()->registered_at);
        $this->assertEquals(3, $noviceA->lessons->firstWhere('id', $lesson->id)->presence->frequency);
        $this->assertEquals(2, $noviceB->lessons->firstWhere('id', $lesson->id)->presence->frequency);
    }

    /** @test */
    public function a_registered_lesson_cannot_be_saved_as_draft()
    {
        $lesson = Lesson::factory()->registered()->create();
        
        $response = $this->postJson('api/lessons/register/' . $lesson->id,
            $this->data()->change('register', 'Trying to save a registered lesson as draft')->get()
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'error' => 'Lesson already registered',
            ]);
        $this->assertNotEquals('Trying to save a registered lesson as draft', $lesson->fresh()->register);
    }

    /** @test */
    public function only_todays_lesson_can_be_saved_as_draft()
    {
        $lesson = Lesson::factory()->notForToday()->create();

        $response = $this->postJson('api/lessons/draft/' . $lesson->id,
            $this->data()->change('presenceList', 'Trying to save as draft an unavailable date lesson')->get()
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'error' => 'Lesson is not available to draft at this date',
            ]);
        $this->assertNotEquals('Trying to save as draft an unavailable date lesson', $lesson->fresh()->register);
    }

    /** @test */
    public function registering_a_draft_lesson()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->forToday()->draft()->create();
        $lesson->enroll($novice);
        
        $response = $this->postJson('api/lessons/register/' . $lesson->id, [
            'register' => 'Example draft lesson register',
            'presenceList' => [
                $novice->id => 3,
            ],
        ]);

        $response->assertStatus(201);
        $this->assertEquals('Example draft lesson register', $lesson->fresh()->register);
        $this->assertNotNull($lesson->fresh()->registered_at);
    }

    /** @test */
    public function registering_a_draft_lesson_wont_duplicate_the_novices_presence()
    {
        $noviceA = User::factory()->create();
        $noviceB = User::factory()->create();
        $lesson = Lesson::factory()->forToday()->create();
        $lesson->enroll($noviceA);
        $lesson->enroll($noviceB);
        $data = $this->data()
                     ->change('presenceList', [
                        $noviceA->id => 1,
                        $noviceB->id => 1,
                     ])
                     ->get();
        $this->postJson('api/lessons/draft/' . $lesson->id, $data);

        $data = $this->data()
                     ->change('presenceList', [
                        $noviceA->id => 3,
                        $noviceB->id => 3,
                     ])
                     ->get();
        $response = $this->postJson('api/lessons/register/' . $lesson->id, $data);

        $response->assertStatus(201);
        $this->assertCount(2, $lesson->fresh()->novices);
    }

    /** @test */
    public function a_user_can_view_the_register_draft()
    {
        $lesson = Lesson::factory()->forToday()->draft()->create([]);

        $reponse = $this->get('lessons/register/create/' . $lesson->id);

        $reponse
            ->assertOk()
            ->assertSee($lesson->register);
    }

    /** @test */
    public function register_field_is_required_to_save_a_draft()
    {
        $lesson = Lesson::factory()->forToday()->create();
        
        $response = $this->postJson(
            'api/lessons/draft/' . $lesson->id,
            $this->data()->exclude('register')->get()
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['register']);
    }

    /** @test */
    public function presence_list_field_is_required_to_save_a_draft()
    {
        $lesson = Lesson::factory()->forToday()->create();
        
        $response = $this->postJson(
            'api/lessons/draft/' . $lesson->id,
            $this->data()->exclude('presenceList')->get()
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['presenceList']);
    }

    /** @test */
    public function saving_a_lesson_as_draft_twice()
    {
        $noviceA = User::factory()->create();
        $noviceB = User::factory()->create();
        $lesson = Lesson::factory()->forToday()->create();
        $lesson->enroll($noviceA);
        $lesson->enroll($noviceB);
        $data = $this->data()
                     ->change('presenceList', [
                        $noviceA->id => 3,
                        $noviceB->id => 3,
                     ])
                     ->get();
        $this->postJson('api/lessons/draft/' . $lesson->id, $data);
        $this->assertEquals(3, $noviceA->lessons->first()->presence->frequency);
        $this->assertEquals(3, $noviceB->lessons->first()->presence->frequency);

        $data = $this->data()
                     ->change('presenceList', [
                        $noviceA->id => 1,
                        $noviceB->id => 1,
                     ])
                     ->get();
        $response = $this->postJson('api/lessons/draft/' . $lesson->id, $data);

        $response->assertStatus(201);
        $this->assertCount(2, $lesson->fresh()->novices);
        $this->assertEquals(1, $noviceA->fresh()->lessons->first()->presence->frequency);
        $this->assertEquals(1, $noviceB->fresh()->lessons->first()->presence->frequency);
    }

    /** @test */
    public function saving_a_lesson_as_draft_twice_with_old_frequency_value()
    {
        $novice = User::factory()->create();
        $lesson = Lesson::factory()->forToday()->create();
        $lesson->enroll($novice);
        $data = $this->data()
                     ->change('presenceList', [
                        $novice->id => 3,
                     ])
                     ->get();
        $this->postJson('api/lessons/draft/' . $lesson->id, $data);
        $this->assertEquals(3, $novice->lessons->first()->presence->frequency);

        $data = $this->data()
                     ->change('presenceList', [
                        $novice->id => 3,
                     ])
                     ->get();
        $response = $this->postJson('api/lessons/draft/' . $lesson->id, $data);

        $response->assertStatus(201);
        $this->assertEquals(3, $novice->fresh()->lessons->first()->presence->frequency);
    }
}
