<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Evaluation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateEvaluationGradeTest extends TestCase
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
        $this->novices->each(function ($novice) {
            $this->lesson->registerFor($novice)->present()->complete();
        });

        $this->lesson->register();
    }

    /** @test */
    public function can_create_grades_for_an_evaluation_wich_lesson_is_registered()
    {
        $response = $this->actingAs($this->instructor)->get(route('evaluations.show', ['evaluation' => $this->evaluation]));

        $response
            ->assertOk()
            ->assertViewIs('evaluations.show')
            ->assertSee($this->evaluation->label)
            ->assertSee($this->evaluation->description)
            ->assertSee(route('evaluations.grades.store', ['evaluation' => $this->evaluation]))
            ->assertSee('registrar indicador')
            ->assertSee("gradesList[{$this->novices[0]->id}]")
            ->assertSee("gradesList[{$this->novices[1]->id}]")
            ->assertSee("gradesList[{$this->novices[2]->id}]");
    }

    /** @test */
    public function cannot_create_grades_for_an_evaluation_wich_lesson_is_not_registered()
    {
        $lesson = Lesson::factory()->hasEvaluation(1)->hasNovices(3)->create();
        $lesson->setTestData();

        $response = $this->actingAs($lesson->instructor)->get(route('evaluations.show', ['evaluation' => $lesson->evaluation]));

        $response
            ->assertOk()
            ->assertDontSee(route('evaluations.grades.store', ['evaluation' => $lesson->evaluation]))
            ->assertDontSee('registrar indicador')
            ->assertDontSee("gradesList[{$lesson->novices[0]->id}]")
            ->assertDontSee("gradesList[{$lesson->novices[1]->id}]")
            ->assertDontSee("gradesList[{$lesson->novices[2]->id}]");
    }

    /** @test */
    public function cannot_create_grades_for_an_evaluation_with_grades_already_recorded()
    {
        $this->evaluation->record([
            $this->novices[0]->id => 'a',
            $this->novices[1]->id => 'a',
            $this->novices[2]->id => 'a',
        ]);

        $response = $this->actingAs($this->instructor)->get(route('evaluations.show', ['evaluation' => $this->evaluation]));

        $response
            ->assertOk()
            ->assertDontSee(route('evaluations.grades.store', ['evaluation' => $this->evaluation]))
            ->assertDontSee('registrar indicador')
            ->assertDontSee("gradesList[{$this->novices[0]->id}]")
            ->assertDontSee("gradesList[{$this->novices[1]->id}]")
            ->assertDontSee("gradesList[{$this->novices[2]->id}]");
    }

    /** @test */
    public function coordinator_cannot_create_grade()
    {
        $coordinator = User::factory()->hasRoles(1, ['name' => 'coordinator'])->create();

        $response = $this->actingAs($coordinator)->get(route('evaluations.show', ['evaluation' => $this->evaluation]));

        $response
            ->assertOk()
            ->assertDontSee(route('evaluations.grades.store', ['evaluation' => $this->evaluation]))
            ->assertDontSee('registrar indicador')
            ->assertDontSee("gradesList[{$this->novices[0]->id}]")
            ->assertDontSee("gradesList[{$this->novices[1]->id}]")
            ->assertDontSee("gradesList[{$this->novices[2]->id}]");
    }

    /** @test */
    public function novice_cannot_create_grade()
    {
        $response = $this->actingAs($this->novices->first())->get(route('evaluations.show', ['evaluation' => $this->evaluation]));

        $response
            ->assertOk()
            ->assertDontSee(route('evaluations.grades.store', ['evaluation' => $this->evaluation]))
            ->assertDontSee('registrar indicador')
            ->assertDontSee("gradesList[{$this->novices[0]->id}]")
            ->assertDontSee("gradesList[{$this->novices[1]->id}]")
            ->assertDontSee("gradesList[{$this->novices[2]->id}]");

    }

    /** @test */
    public function employer_cannot_create_grade()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('evaluations.show', [
                             'evaluation' => $this->evaluation
                         ]));

        $response
            ->assertOk()
            ->assertDontSee(route('evaluations.grades.store', ['evaluation' => $this->evaluation]))
            ->assertDontSee('registrar indicador')
            ->assertDontSee("gradesList[{$this->novices[0]->id}]")
            ->assertDontSee("gradesList[{$this->novices[1]->id}]")
            ->assertDontSee("gradesList[{$this->novices[2]->id}]");
    }
}
