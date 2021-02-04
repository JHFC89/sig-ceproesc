<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;

class LeftSideNavigationBarTest extends TestCase
{
    protected $coordinator;

    protected $instructor;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create();

        $this->instructor = User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create();

        $this->novice = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create();

        $this->employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create();
    }

    /** @test */
    public function coordinator_can_view_links_related_to_disciplines()
    {
        $response = $this
            ->actingAs($this->coordinator)
            ->get(route('dashboard'));

        $response
            ->assertSee(route('disciplines.index'))
            ->assertSee(route('disciplines.create'));
    }

    /** @test */
    public function instructors_cannot_view_links_related_to_disciplines()
    {
        $response = $this
            ->actingAs($this->instructor)
            ->get(route('dashboard'));

        $response
            ->assertDontSee(route('disciplines.index'))
            ->assertDontSee(route('disciplines.create'));
    }

    /** @test */
    public function novice_cannot_view_links_related_to_disciplines()
    {
        $response = $this
            ->actingAs($this->novice)
            ->get(route('dashboard'));

        $response
            ->assertDontSee(route('disciplines.index'))
            ->assertDontSee(route('disciplines.create'));
    }

    /** @test */
    public function employer_cannot_view_links_related_to_disciplines()
    {
        $response = $this
            ->actingAs($this->employer)
            ->get(route('dashboard'));

        $response
            ->assertDontSee(route('disciplines.index'))
            ->assertDontSee(route('disciplines.create'));
    }
}
