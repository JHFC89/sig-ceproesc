<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecordEvaluationGradeTest extends TestCase
{
    use RefreshDatabase;

    protected $lesson;

    protected $evaluation;

    protected $instructor;

    protected $novices;

    protected $data;

    protected function setUp():void
    {
        parent::setUp();

        $this->lesson = Lesson::factory()->hasEvaluation(1)->hasNovices(3)->create();
        $this->lesson->setTestData();

        $this->evaluation = $this->lesson->evaluation;

        $this->lesson->register();

        $this->instructor = $this->lesson->instructor;

        $this->novices = $this->lesson->novices;

        $this->data = [
            'gradesList' => [
                $this->novices[0]->id => 'a',
                $this->novices[1]->id => 'b',
                $this->novices[2]->id => 'c',
            ],
        ];
    }

    /** @test */
    public function instructor_can_record_grades_for_his_evaluation()
    {
        $data = [
            'gradesList' => [
                $this->novices[0]->id => 'a',
                $this->novices[1]->id => 'b',
                $this->novices[2]->id => 'c',
            ],
        ];

        $response = $this->actingAs($this->instructor)->post(route('evaluations.grades.store', ['evaluation' => $this->evaluation]), $data);

        $response->assertOk();
        $this->assertEquals('a', $this->evaluation->gradeForNovice($this->novices[0]));
        $this->assertEquals('b', $this->evaluation->gradeForNovice($this->novices[1]));
        $this->assertEquals('c', $this->evaluation->gradeForNovice($this->novices[2]));
    }

    /** @test */
    public function instructor_cannot_record_grades_for_another_instructor_evaluation()
    {
        $anotherInstrutor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $response = $this->actingAs($anotherInstrutor)->post(route('evaluations.grades.store', ['evaluation' => $this->evaluation]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function lesson_must_be_registered_to_be_able_to_record_grades()
    {
        $lesson = Lesson::factory()->hasEvaluation(1)->hasNovices(3)->create();
        $lesson->setTestData();

        $response = $this->actingAs($lesson->instructor)->post(route('evaluations.grades.store', ['evaluation' => $lesson->evaluation]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_record_grades()
    {
        $response = $this->post(route('evaluations.grades.store', ['evaluation' => $this->evaluation]), $this->data);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function novice_cannot_record_grades()
    {
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();
        
        $response = $this->actingAs($novice)->post(route('evaluations.grades.store', ['evaluation' => $this->evaluation]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function coordinator_cannot_record_grades()
    {
        $coordinator = User::factory()->hasRoles(1, ['name' => 'coordinator'])->create();
        
        $response = $this->actingAs($coordinator)->post(route('evaluations.grades.store', ['evaluation' => $this->evaluation]), $this->data);

        $response->assertUnauthorized();
    }
        
    /** @test */
    public function employer_cannot_record_grades()
    {
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])->create();
        
        $response = $this->actingAs($employer)->post(route('evaluations.grades.store', ['evaluation' => $this->evaluation]), $this->data);

        $response->assertUnauthorized();
    }
        
    /** @test */
    public function user_without_role_cannot_record_grades()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post(route('evaluations.grades.store', ['evaluation' => $this->evaluation]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function grades_list_is_required()
    {
        $data = [];
        $user = User::factory()->create();

        $response = $this->actingAs($this->instructor)->post(route('evaluations.grades.store', ['evaluation' => $this->evaluation]), $data);

        $response->assertSessionHasErrors('gradesList');
    }

    /** @test */
    public function grades_list_values_must_be_string()
    {
        $user = User::factory()->create();
        $data = [
            'gradesList' => [
                'novice_1' => 1,
                'novice_2' => true,
                'novice_3' => null,
            ],
        ];

        $response = $this->actingAs($this->instructor)->post(route('evaluations.grades.store', ['evaluation' => $this->evaluation]), $data);

        $response->assertSessionHasErrors('gradesList.*');
    }
}
