<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InstructorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_check_an_user_is_instructor()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $user = User::factory()->create();

        $resultForInstructor = $instructor->isInstructor();
        $resultForNotInstructor = $user->isInstructor();

        $this->assertTrue($resultForInstructor);
        $this->assertFalse($resultForNotInstructor);
    }
}
