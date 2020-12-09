<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class EmployerTest extends TestCase
{
    /** @test */
    public function can_check_is_employer()
    {
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])->create();

        $this->assertTrue($employer->isEmployer());
    }

    /** @test */
    public function can_check_is_employer_of_a_specific_novice()
    {
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])->create();
        $noviceForEmployer = User::factory()->hasRoles(1, ['name' => 'novice'])->create();
        $noviceNotForEmployer = User::factory()->hasRoles(1, ['name' => 'novice'])->create();
        $employer->novices()->save($noviceForEmployer);

        $noviceForEmployerResult = $employer->isEmployerOf($noviceForEmployer);
        $noviceNotForEmployerResult = $employer->isEmployerOf($noviceNotForEmployer);
        
        $this->assertTrue($noviceForEmployerResult);
        $this->assertFalse($noviceNotForEmployerResult);
    }
}
