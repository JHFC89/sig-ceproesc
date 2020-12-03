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
}
