<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use Tests\Traits\LessonTestData;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DraftLessonTest extends TestCase
{
    use RefreshDatabase, LessonTestData;

    private $user;

    protected function setUp():void
    {
        parent::setUp();
        $this->instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
    }

    /** @test */
    public function a_lesson_can_be_saved_as_draft_by_the_assigned_instructor()
    {
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();
        $lesson = Lesson::factory()
            ->forToday()
            ->hasNovices(2)
            ->create([
                'instructor_id' => $instructor->id,
            ]);
        extract($lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');
        $data = $this->data()
                     ->change('register', 'Example lesson register draft')
                     ->change('presenceList', [
                        $novice_0->id => 3,
                        $novice_1->id => 2,
                     ])
                     ->get();
        
        $response = $this->actingAs($instructor, 'api')->postJson('api/lessons/draft/' . $lesson->id, $data);

        $response->assertStatus(201);
        $this->assertEquals('Example lesson register draft', $lesson->fresh()->register);
        $this->assertNull($lesson->fresh()->registered_at);
        $this->assertEquals(3, $novice_0->lessons->firstWhere('id', $lesson->id)->presence->frequency);
        $this->assertEquals(2, $novice_1->lessons->firstWhere('id', $lesson->id)->presence->frequency);
    }

    /** @test */
    public function a_registered_lesson_cannot_be_saved_as_draft()
    {
        $lesson = Lesson::factory()->registered()->create(['instructor_id' => $this->instructor->id]);
        
        $response = $this
            ->actingAs($this->instructor, 'api')
            ->postJson(
                'api/lessons/draft/' . $lesson->id,
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
        $lesson = Lesson::factory()->notForToday()->create(['instructor_id' => $this->instructor->id]);

        $response = $this->actingAs($this->instructor, 'api')
                         ->postJson(
                             'api/lessons/draft/' . $lesson->id,
                             $this->data()
                                  ->change('presenceList', 'Trying to save as draft an unavailable date lesson')
                                  ->get()
                        );

        $response
            ->assertStatus(422)
            ->assertJson(['error' => 'Lesson is not available to draft at this date']);
        $this->assertNotEquals('Trying to save as draft an unavailable date lesson', $lesson->fresh()->register);
    }

    /** @test */
    public function a_guest_cannot_save_a_lesson_as_draft()
    {
        $lesson = Lesson::factory()->forToday()->hasNovices(2)->create();
        $data = $this->data()->lesson($lesson)->get();
        
        $response = $this->postJson('api/lessons/draft/' . $lesson->id, $data);

        $response->assertUnauthorized();
        $this->assertNull($lesson->fresh()->register);
    }

    /** @test */
    public function a_lesson_cannot_be_saved_by_an_user_thats_not_an_instructor()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->forToday()->hasNovices(2)->create();
        $data = $this->data()->lesson($lesson)->get();
        
        $response = $this->actingAs($user, 'api')->postJson('api/lessons/draft/' . $lesson->id, $data);

        $response
            ->assertUnauthorized()
            ->assertJson(['error' => 'Action not authorized for this user']);
        $this->assertNull($lesson->fresh()->register);
    }

    /** @test */
    public function a_lesson_cannot_be_saved_as_draft_by_an_instructor_thats_not_assigned_to_it()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lessonForAnotherInstructor = Lesson::factory()->forToday()->hasNovices(2)->create();
        $data = $this->data()->lesson($lessonForAnotherInstructor)->get();
        
        $response = $this->actingAs($instructor, 'api')->postJson('api/lessons/draft/' . $lessonForAnotherInstructor->id, $data);

        $response
            ->assertUnauthorized()
            ->assertJson(['error' => 'Action not authorized for this instructor']);
        $this->assertNull($lessonForAnotherInstructor->fresh()->register);
    }

    /** @test */
    public function register_field_is_required_to_save_a_draft()
    {
        $lesson = Lesson::factory()->forToday()->create(['instructor_id' => $this->instructor->id]);
        
        $response = $this->actingAs($this->instructor, 'api')->postJson(
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
        $lesson = Lesson::factory()->forToday()->create(['instructor_id' => $this->instructor->id]);
        
        $response = $this->actingAs($this->instructor, 'api')->postJson(
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
        $lesson = Lesson::factory()
            ->forToday()
            ->hasNovices(2)
            ->create(['instructor_id' => $this->instructor->id]);
        extract($lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');
        $data = $this->data()
                     ->change('presenceList', [
                        $novice_0->id => 3,
                        $novice_1->id => 3,
                     ])
                     ->get();
        $this->actingAs($this->instructor, 'api')->postJson('api/lessons/draft/' . $lesson->id, $data);
        $this->assertEquals(3, $novice_0->lessons->first()->presence->frequency);
        $this->assertEquals(3, $novice_1->lessons->first()->presence->frequency);

        $data = $this->data()
                     ->change('presenceList', [
                        $novice_0->id => 1,
                        $novice_1->id => 1,
                     ])
                     ->get();
        $response = $this->actingAs($this->instructor, 'api')->postJson('api/lessons/draft/' . $lesson->id, $data);

        $response->assertStatus(201);
        $this->assertCount(2, $lesson->fresh()->novices);
        $this->assertEquals(1, $novice_0->fresh()->lessons->first()->presence->frequency);
        $this->assertEquals(1, $novice_1->fresh()->lessons->first()->presence->frequency);
    }

    /** @test */
    public function saving_a_lesson_as_draft_twice_while_keeping_the_same_frequency_value()
    {
        $lesson = Lesson::factory()->forToday()->hasNovices(1)->create(['instructor_id' => $this->instructor->id]);
        $novice = $lesson->novices->first();
        $data = $this->data()
                     ->change('presenceList', [
                        $novice->id => 3,
                     ])
                     ->get();
        $this->actingAs($this->instructor, 'api')->postJson('api/lessons/draft/' . $lesson->id, $data);
        $this->assertEquals(3, $novice->lessons->first()->presence->frequency);

        $data = $this->data()
                     ->change('presenceList', [
                        $novice->id => 3,
                     ])
                     ->get();
        $response = $this->actingAs($this->instructor, 'api')->postJson('api/lessons/draft/' . $lesson->id, $data);

        $response->assertStatus(201);
        $this->assertEquals(3, $novice->fresh()->lessons->first()->presence->frequency);
    }
}
