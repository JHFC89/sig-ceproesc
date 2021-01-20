<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Evaluation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewEvaluationTest extends TestCase
{
    use RefreshDatabase;

    protected $lesson;

    protected $evaluation;

    protected $instructor;

    protected $novices;

    protected function setUp():void
    {
        parent::setUp();

        $this->lesson = Lesson::factory()->hasEvaluation(1)->hasNovices(3)->create();
        $this->lesson->setTestData();

        $this->evaluation = $this->lesson->evaluation;

        $this->instructor = $this->lesson->instructor;

        $this->novices = $this->lesson->novices;
    }

    /** @test */
    public function instrutor_can_view_an_evaluation()
    {
        $response = $this->actingAs($this->instructor)->get(route('evaluations.show', ['evaluation' => $this->evaluation]));

        $response
            ->assertOk()
            ->assertViewIs('evaluations.show')
            ->assertViewHas('evaluation')
            ->assertSee('atividade avaliativa')
            ->assertSee($this->lesson->formatted_date)
            ->assertSee(route('lessons.show', ['lesson' => $this->lesson]))
            ->assertSee($this->evaluation->label)
            ->assertSee($this->evaluation->description)
            ->assertSee($this->novices[0]->code)
            ->assertSee($this->novices[0]->name)
            ->assertSee($this->novices[0]->class)
            ->assertSee($this->novices[1]->code)
            ->assertSee($this->novices[1]->name)
            ->assertSee($this->novices[1]->class)
            ->assertSee($this->novices[2]->code)
            ->assertSee($this->novices[2]->name)
            ->assertSee($this->novices[2]->class);
    }

    /** @test */
    public function instructor_cannot_see_an_evaluation_he_did_not_create()
    {
        $anotherInstrutor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $response = $this->actingAs($anotherInstrutor)->get(route('evaluations.show', ['evaluation' => $this->evaluation]));
        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_view_an_evaluation()
    {
        $response = $this->get(route('evaluations.show', ['evaluation' => $this->evaluation]));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_without_role_cannot_view_an_evaluation()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('evaluations.show', ['evaluation' => $this->evaluation]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function coordinator_can_view_an_evaluation()
    {
        $coordinator = User::factory()->hasRoles(1, ['name' => 'coordinator'])->create();

        $response = $this->actingAs($coordinator)->get(route('evaluations.show', ['evaluation' => $this->evaluation]));
        $response
            ->assertOk()
            ->assertViewIs('evaluations.show')
            ->assertViewHas('evaluation')
            ->assertSee('atividade avaliativa')
            ->assertSee($this->lesson->formatted_date)
            ->assertSee(route('lessons.show', ['lesson' => $this->lesson]))
            ->assertSee($this->evaluation->label)
            ->assertSee($this->evaluation->description)
            ->assertSee($this->novices[0]->code)
            ->assertSee($this->novices[0]->name)
            ->assertSee($this->novices[0]->class)
            ->assertSee($this->novices[1]->code)
            ->assertSee($this->novices[1]->name)
            ->assertSee($this->novices[1]->class)
            ->assertSee($this->novices[2]->code)
            ->assertSee($this->novices[2]->name)
            ->assertSee($this->novices[2]->class);
    }

    /** @test */
    public function novice_can_view_an_evaluation_informations_for_him()
    {
        $novice = $this->lesson->novices->first();

        $response = $this->actingAs($novice)->get(route('evaluations.show', ['evaluation' => $this->evaluation]));
        $response
            ->assertOk()
            ->assertViewIs('evaluations.show')
            ->assertViewHas('evaluation')
            ->assertSee('atividade avaliativa')
            ->assertSee($this->lesson->formatted_date)
            ->assertSee(route('lessons.show', ['lesson' => $this->lesson]))
            ->assertSee($this->evaluation->label)
            ->assertSee($this->evaluation->description)
            ->assertDontSee($this->novices[0]->code)
            ->assertDontSee($this->novices[0]->name)
            ->assertDontSee($this->novices[0]->class)
            ->assertDontSee($this->novices[1]->code)
            ->assertDontSee($this->novices[1]->name)
            ->assertDontSee($this->novices[1]->class)
            ->assertDontSee($this->novices[2]->code)
            ->assertDontSee($this->novices[2]->name)
            ->assertDontSee($this->novices[2]->class);
    }

    /** @test */
    public function employer_can_view_only_the_evaluations_informations_that_belongs_to_his_novices()
    {
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])->create();
        $employer->novices()->saveMany([$this->novices[0], $this->novices[1]]);

        $response = $this->actingAs($employer)->get(route('evaluations.show', ['evaluation' => $this->evaluation]));
        $response
            ->assertOk()
            ->assertViewIs('evaluations.show')
            ->assertViewHas('evaluation')
            ->assertSee('atividade avaliativa')
            ->assertSee($this->lesson->formatted_date)
            ->assertSee(route('lessons.show', ['lesson' => $this->lesson]))
            ->assertSee($this->evaluation->label)
            ->assertSee($this->evaluation->description)
            ->assertSee($this->novices[0]->code)
            ->assertSee($this->novices[0]->name)
            ->assertSee($this->novices[1]->code)
            ->assertSee($this->novices[1]->name)
            ->assertDontSee($this->novices[2]->code)
            ->assertDontSee($this->novices[2]->name);
    }
}
