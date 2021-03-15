<?php

namespace Tests\Unit;

use App\Models\Holiday;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HolidayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_all_holidays_for_a_city()
    {
        Holiday::factory()->create([
            'name' => 'fake national holiday',
            'date' => now()->addMonth(),
        ]);
        Holiday::factory()->create([
            'name'  => 'fake local holiday A',
            'date'  => now()->addMonths(2),
            'local' => 'city A']
        );
        Holiday::factory()->count(1)->create([
            'name'  => 'fake local holiday B',
            'date'  => now()->addMonths(3),
            'local' => 'city B']
        );

        $result = Holiday::allForCity('city A');

        $this->assertEquals(2, $result->count());
        $this->assertEquals('fake national holiday', $result->first()->name);
        $this->assertEquals('fake local holiday A', $result->last()->name);
    }
}
