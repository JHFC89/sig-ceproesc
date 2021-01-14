<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Evaluation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreEvaluationTest extends TestCase
{
    use RefreshDatabase;

    protected $lesson;

    protected $data;
        
    protected $instructor;

    protected function setUp():void
    {
        parent::setUp();

        $this->instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $this->lesson = Lesson::factory()->instructor($this->instructor)->create();

        $this->data = [
            'label' => 'test evaluation',
            'description' => 'test description',
        ];
    }

    /** @test */
    public function instructor_can_store_an_evaluation()
    {
        $response = $this->actingAs($this->instructor)->post(route('lessons.evaluations.store', ['lesson' => $this->lesson]), $this->data);

        $response
            ->assertOk()
            ->assertViewIs('evaluations.show');
        $this->assertEquals(1, Evaluation::count());
        $evaluation = Evaluation::first();
        $this->assertEquals('test evaluation', $evaluation->label);
        $this->assertEquals('test description', $evaluation->description);
        $this->assertEquals($this->lesson->id, $evaluation->lesson->id);
    }

    /** @test */
    public function only_the_instructor_for_the_lesson_can_store_an_evaluation_for_it()
    {
        $instructorForAnotherLesson = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $response = $this->actingAs($instructorForAnotherLesson)->post(route('lessons.evaluations.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(0, Evaluation::count());
    }

    /** @test */
    public function cannot_store_an_evaluation_if_the_lesson_already_has_one()
    {
        $this->lesson->evaluation()->create(['label' => 'test label', 'description' => 'test description']);

        $response = $this->actingAs($this->instructor)->post(route('lessons.evaluations.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(1, Evaluation::count());
    }

    /** @test */
    public function guest_cannot_store_an_evaluation()
    {
        $response = $this->post(route('lessons.evaluations.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertRedirect(route('login'));
        $this->assertEquals(0, Evaluation::count());
    }

    /** @test */
    public function coordinator_cannot_store_an_evaluation()
    {
        $coordinator = User::factory()->hasRoles(1, ['name' => 'coordinator'])->create();

        $response = $this->actingAs($coordinator)->post(route('lessons.evaluations.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(0, Evaluation::count());
    }

    /** @test */
    public function employer_cannot_store_an_evaluation()
    {
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])->create();

        $response = $this->actingAs($employer)->post(route('lessons.evaluations.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(0, Evaluation::count());
    }
    
    /** @test */
    public function novice_cannot_store_an_evaluation()
    {
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();

        $response = $this->actingAs($novice)->post(route('lessons.evaluations.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(0, Evaluation::count());
    }
    
    /** @test */
    public function user_cannot_store_an_evaluation()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('lessons.evaluations.store', ['lesson' => $this->lesson]), $this->data);

        $response->assertUnauthorized();
        $this->assertEquals(0, Evaluation::count());
    }
    //instructor can

    /** @test */
    public function label_is_required()
    {
        $data = [
            'description' => 'test description',
        ];

        $response = $this->actingAs($this->instructor)->post(route('lessons.evaluations.store', ['lesson' => $this->lesson]), $data);

        $response->assertSessionHasErrors(['label']);
        $this->assertEquals(0, Evaluation::count());
    }

    /** @test */
    public function label_must_be_a_string()
    {
        $data = [
            'label' => 1,
            'description' => 'test description',
        ];

        $response = $this->actingAs($this->instructor)->post(route('lessons.evaluations.store', ['lesson' => $this->lesson]), $data);

        $response->assertSessionHasErrors(['label']);
        $this->assertEquals(0, Evaluation::count());
    }

    /** @test */
    public function description_is_required()
    {
        $data = [
            'label' => 'test label',
        ];

        $response = $this->actingAs($this->instructor)->post(route('lessons.evaluations.store', ['lesson' => $this->lesson]), $data);

        $response->assertSessionHasErrors(['description']);
        $this->assertEquals(0, Evaluation::count());
    }

    /** @test */
    public function description_must_be_a_string()
    {
        $data = [
            'label' => 'test label',
            'description' => 1,
        ];

        $response = $this->actingAs($this->instructor)->post(route('lessons.evaluations.store', ['lesson' => $this->lesson]), $data);

        $response->assertSessionHasErrors(['description']);
        $this->assertEquals(0, Evaluation::count());
    }
}
