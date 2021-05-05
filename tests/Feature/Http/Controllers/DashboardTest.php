<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function viewing_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
    }

    /** @test */
    public function guest_cannot_see_dashboard()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function instrutor_can_see_a_card_with_lessons_for_today()
    {
        $instrutor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();
        $lessons = Lesson::factory()->forToday()->instructor($instrutor)->hasNovices(3)->create();

        $response = $this->actingAs($instrutor)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('aulas de hoje')
            ->assertSee($lessons->first()->discipline->name)
            ->assertDontSee($lessons->first()->discipline);
    }
}
