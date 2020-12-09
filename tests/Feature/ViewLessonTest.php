<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewLessonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function instructor_can_view_a_not_registered_lesson_he_is_assigned_to()
    {
        $date = Carbon::now();
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create(['name' => 'John Doe']);
        $lesson = Lesson::factory()
            ->notRegistered()
            ->instructor($instructor)
            ->hasNovices(3)
            ->create([
                'date'          => $date,
                'class'         => '2021 - janeiro',
                'discipline'    => 'administração',
                'hourly_load'   => '123hr',
        ]);
        extract($lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');

        $response = $this->actingAs($instructor)->get('lessons/' . $lesson->id);

        $response
            ->assertOk()
            ->assertSee('John Doe')
            ->assertSee($date->format('d/m/Y'))
            ->assertSee('2021 - janeiro')
            ->assertSee('administração')
            ->assertSee('123hr')
            ->assertDontSee('"presença"')
            ->assertDontSee('"observação"')
            ->assertDontSee('Nenhuma observação registrada')
            ->assertSee($novice_0->name)
            ->assertSee($novice_0->code)
            ->assertSee($novice_1->name)
            ->assertSee($novice_1->code)
            ->assertSee($novice_2->name)
            ->assertSee($novice_2->code);
    }

    /** @test */
    public function instructor_can_view_a_registered_lesson_he_is_assigned_to()
    {
        $instructor = User::factory()->hasRoles(['name' => 'instructor'])->create();
        $lesson = Lesson::factory()->notRegistered()->instructor($instructor)->hasNovices(3)->create();
        extract($lesson->novices->all(), EXTR_PREFIX_ALL, 'novice');
        $lesson->registerFor($novice_0)->present()->observation('Test observation for novice_0')->complete();
        $lesson->registerFor($novice_1)->absent()->observation('Test observation for novice_1')->complete();
        $lesson->registerFor($novice_2)->present()->complete();
        $lesson->registered_at = now();
        $lesson->save();

        $response = $this->actingAs($instructor)->get('lessons/' . $lesson->id);

        $response
            ->assertOk()
            ->assertSee($instructor->name)
            ->assertSee($lesson->formatted_date)
            ->assertSee('presença')
            ->assertSee('observação')
            ->assertSee('Test observation for novice_0')
            ->assertSee('Test observation for novice_1')
            ->assertSee('Nenhuma observação registrada');

    }

    /** @test */
    public function instructor_cannot_view_a_lesson_he_is_not_assigned_to()
    {
        $instructor = User::factory()->hasRoles(['name' => 'instructor'])->create();
        $lessonForAnotherInstructor = Lesson::factory()->notRegistered()->hasNovices(3)->create();
        
        $response = $this->actingAs($instructor)->get('lessons/' . $lessonForAnotherInstructor->id);

        $response->assertNotFound();
    }

    /** @test */
    public function guest_cannot_view_any_lesson()
    {
        $lesson = Lesson::factory()->hasNovices(2)->create();

        $response = $this->get('lesson/' . $lesson->id);

        $response->assertNotFound();
    }

    /** @test */
    public function user_with_no_permissions_cannot_view_any_lesson()
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('lessons/' . $lesson->id);

        $response->assertNotFound();
    }

    /** @test */
    public function novice_can_view_a_not_registered_lesson_he_is_enrolled()
    {
        $lesson = Lesson::factory()->hasNovices(1)->notRegistered()->create();
        $novice = $lesson->novices->first();
        $novice->roles()->attach(Role::firstOrCreate(['name' => 'novice'])->id);
        
        $response = $this->actingAs($novice)->get('lessons/' . $lesson->id);

        $response
            ->assertOk()
            ->assertSee($novice->name)
            ->assertSee($lesson->instructor->name)
            ->assertSee($lesson->formatted_date)
            ->assertSee($lesson->class)
            ->assertDontSee('observação')
            ->assertDontSee('presença')
            ->assertDontSee('registro');
    }

    /** @test */
    public function novice_can_view_a_lesson_he_is_enrolled_to()
    {
        $lesson = Lesson::factory()->hasNovices(1)->notRegistered()->create();
        $novice = $lesson->novices->first();
        $novice->roles()->attach(Role::firstOrCreate(['name' => 'novice'])->id);
        $lesson->registerFor($novice)->present()->observation('Test Novice observation')->complete();
        $lesson->registered_at = now();
        $lesson->save();

        $response = $this->actingAs($novice)->get('lessons/' . $lesson->id);

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

        $response = $this->actingAs($novice)->get('lessons/' . $lessonForAnotherNovice->id);

        $response->assertNotFound();
    }

    /** @test */
    public function novice_cannot_view_another_novice_informations_for_the_same_lesson()
    {
        $lesson = Lesson::factory()->hasNovices(2)->notRegistered()->create();
        $noviceA = $lesson->novices->first();
        $noviceA->roles()->attach(Role::firstOrCreate(['name' => 'novice'])->id);
        $noviceB = $lesson->novices->last();
        $lesson->registerFor($noviceA)->present()->observation('Test Novice A observation')->complete();
        $lesson->registerFor($noviceB)->absent()->observation('Test Novice B observation')->complete();
        $lesson->registered_at = now();
        $lesson->save();

        $response = $this->actingAs($noviceA)->get('lessons/' . $lesson->id);

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
    public function employer_can_view_his_novices_informations_for_a_not_registered_lesson_they_are_enrolled()
    {
        $novices = User::factory()->hasRoles(1, ['name' => 'novice'])->count(3)->create();
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])->create();
        $employer->novices()->saveMany($novices->all());
        $lesson = Lesson::factory()->notRegistered()->create();
        $novices->each(function ($novice) use ($lesson) {
            $lesson->enroll($novice);
        });
        
        $response = $this->actingAs($employer)->get('lessons/' . $lesson->id);

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
        $novices = User::factory()->hasRoles(1, ['name' => 'novice'])->count(3)->create();
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])->create();
        $employer->novices()->saveMany($novices->all());
        $lesson = Lesson::factory()->notRegistered()->create();
        $novices->each(function ($novice) use ($lesson) {
            $lesson->enroll($novice);
        });
        $lesson->registerFor($novices[0])->present()->observation('Test observation for first novice')->complete();
        $lesson->registerFor($novices[1])->absent()->observation('Test observation for second novice')->complete();
        $lesson->registerFor($novices[2])->present()->complete();
        $lesson->registered_at = now();
        $lesson->save();
        
        $response = $this->actingAs($employer)->get('lessons/' . $lesson->id);

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
        $novicesForEmployerA = User::factory()->hasRoles(1, ['name' => 'novice'])->count(3)->create();
        $employerA = User::factory()->hasRoles(1, ['name' => 'employer'])->create();
        $employerA->novices()->saveMany($novicesForEmployerA->all());
        $novicesForEmployerB = User::factory()->hasRoles(1, ['name' => 'novice'])->count(3)->create();
        $employerB = User::factory()->hasRoles(1, ['name' => 'employer'])->create();
        $employerB->novices()->saveMany($novicesForEmployerB->all());
        $lesson = Lesson::factory()->notRegistered()->create();
        collect([$novicesForEmployerA, $novicesForEmployerB])->flatten()->each(function ($novice) use ($lesson) {
            $lesson->enroll($novice);
        });
        
        $response = $this->actingAs($employerA)->get('lessons/' . $lesson->id);

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
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])->create();
        $lesson = Lesson::factory()->hasNovices(3)->notRegistered()->create();

        $response = $this->actingAs($employer)->get('lessons/' . $lesson->id);

        $response->assertNotFound();
    }
}
