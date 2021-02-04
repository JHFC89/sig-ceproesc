<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Discipline;
use App\Exceptions\NotInstructorException;
use Illuminate\Foundation\Testing\RefreshDatabase;


class DisciplineTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_check_it_is_basic()
    {
        $discipline = Discipline::factory()->basic()->create();

        $result = $discipline->isBasic();

        $this->assertTrue($result);
    }

    /** @test */
    public function can_check_it_is_specific()
    {
        $discipline = Discipline::factory()->specific()->create();

        $result = $discipline->isSpecific();

        $this->assertTrue($result);
    }

    /** @test */
    public function can_get_the_type_attribute()
    {
        $basicDiscipline = Discipline::factory()->basic()->create();
        $specificDiscipline = Discipline::factory()->specific()->create();

        $resultForBasic = $basicDiscipline->type;
        $resultForSpecific = $specificDiscipline->type;

        $this->assertEquals('básico', $resultForBasic);
        $this->assertEquals('específico', $resultForSpecific);
    }

    /** @test */
    public function can_attach_instructors_by_ids()
    {
        $discipline = Discipline::factory()->create();
        $instructors = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->count(3)
            ->create();

        $discipline->attachInstructors($instructors->pluck('id')->toArray());

        $this->assertEquals(
            $instructors->pluck('id'), 
            $discipline->instructors->pluck('id')
        );
    }

    /** @test */
    public function can_attach_one_instructor_by_id()
    {
        $discipline = Discipline::factory()->create();
        $instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $discipline->attachInstructors($instructor->id);

        $this->assertEquals(
            $discipline->instructors->first()->id,
            $instructor->id
        );
    }

    /** @test */
    public function attaching_new_instructors_will_reset_the_attached_ones()
    {
        $discipline = Discipline::factory()->hasInstructors(3)->create();
        $oldInstructors = $discipline->instructors->map(function ($instructor) {
            $instructor->turnIntoInstructor();
            return $instructor;
        });
        $newInstructors = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->count(3)
            ->create();

        $discipline->attachInstructors($newInstructors->pluck('id')->toArray());

        $discipline->refresh();
        $this->assertTrue($discipline->isAttached($newInstructors[0]));
        $this->assertTrue($discipline->isAttached($newInstructors[1]));
        $this->assertTrue($discipline->isAttached($newInstructors[2]));
        $this->assertFalse($discipline->isAttached($oldInstructors[0]));
        $this->assertFalse($discipline->isAttached($oldInstructors[1]));
        $this->assertFalse($discipline->isAttached($oldInstructors[2]));
    }

    /** @test */
    public function trying_to_attach_non_instructor_users_should_throw_an_exception()
    {
        $discipline = Discipline::factory()->create();
        $notInstructor = User::factory()->count(3)->create();

        try {
            $discipline->attachInstructors($notInstructor->pluck('id')->toArray());
        } catch (NotInstructorException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Trying to attach a non instructor user to a discipline should throw an exception');
    }

    /** @test */
    public function can_check_an_instructor_is_attached()
    {
        $discipline = Discipline::factory()->create();
        $instructors = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->count(2)
            ->create();
        $attachedInstructor = $instructors->first();
        $discipline->attachInstructors($attachedInstructor->id);
        $notAttachedInstructor = $instructors->last();

        $resultForAttachedInstructor = $discipline->isAttached($attachedInstructor);
        $resultForNotAttachedInstructor = $discipline->isAttached($notAttachedInstructor);

        $this->assertTrue($resultForAttachedInstructor);
        $this->assertFalse($resultForNotAttachedInstructor);
    }
}
