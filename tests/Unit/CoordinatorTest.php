<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CoordinatorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_check_it_is_a_coordinator()
    {
        $coordinator = User::factory()->hasRoles(1, ['name' => 'coordinator'])->create();
        $notCoordinator = User::factory()->create();
        
        $resultForCoordinator = $coordinator->isCoordinator();
        $resultForNotCoordinator = $notCoordinator->isCoordinator();

        $this->assertTrue($resultForCoordinator);
        $this->assertFalse($resultForNotCoordinator);
    }
}
