<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeftSideNavigationBarTest extends TestCase
{
    protected $admin;

    protected $coordinator;

    protected $instructor;

    protected $novice;

    protected $empoyer;

    protected function setUp():void
    {
        parent::setUp();

        $this->admin = User::fakeAdmin();

        $this->coordinator = User::fakeCoordinator();

        $this->instructor = User::fakeInstructor();

        $this->novice = User::fakeNovice();

        $this->employer = User::fakeEmployer();
    }

    use RefreshDatabase;

    /** @test */
    public function admin_can_view_links_related_to_coordinators()
    {
        $response = $this
            ->actingAs($this->admin)->get(route('dashboard'));

        $response->assertSee(route('coordinators.index'))
                 ->assertSee(route('coordinators.create'));
    }

    /** @test */
    public function admin_can_view_links_related_to_admins()
    {
        $response = $this
            ->actingAs($this->admin)->get(route('dashboard'));

        $response->assertSee(route('admins.index'))
                 ->assertSee(route('admins.create'));
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
    public function coordinator_can_view_links_related_to_courses()
    {
        $response = $this
            ->actingAs($this->coordinator)
            ->get(route('dashboard'));

        $response
            ->assertSee(route('courses.index'))
            ->assertSee(route('courses.create'));
    }

    /** @test */
    public function coordinator_can_view_link_to_view_holidays()
    {
        $response = $this
            ->actingAs($this->coordinator)
            ->get(route('dashboard'));

        $response->assertSee(route('holidays.index'));
    }

    /** @test */
    public function coordinator_can_view_links_related_to_courses_classes()
    {
        $response = $this
            ->actingAs($this->coordinator)
            ->get(route('dashboard'));

        $response
            ->assertSee(route('classes.index'))
            ->assertSee(route('classes.create'));
    }

    /** @test */
    public function coordinator_can_view_links_related_to_companies()
    {
        $response = $this
            ->actingAs($this->coordinator)
            ->get(route('dashboard'));

        $response
            ->assertSee(route('companies.index'))
            ->assertSee(route('companies.create'));
    }

    /** @test */
    public function coordinator_can_view_links_related_to_instructors()
    {
        $response = $this
            ->actingAs($this->coordinator)
            ->get(route('dashboard'));

        $response
            ->assertSee(route('instructors.index'))
            ->assertSee(route('instructors.create'));
    }

    /** @test */
    public function coordinator_cannot_view_links_related_to_coordinators()
    {
        $response = $this
            ->actingAs($this->coordinator)->get(route('dashboard'));

        $response->assertDontSee(route('coordinators.index'))
                 ->assertDontSee(route('coordinators.create'));
    }

    /** @test */
    public function coordinator_cannot_view_links_related_to_admins()
    {
        $response = $this
            ->actingAs($this->coordinator)->get(route('dashboard'));

        $response->assertDontSee(route('admins.index'))
                 ->assertDontSee(route('admins.create'));
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
    public function instructors_cannot_view_links_related_to_courses()
    {
        $response = $this
            ->actingAs($this->instructor)
            ->get(route('dashboard'));

        $response
            ->assertDontSee(route('courses.index'))
            ->assertDontSee(route('courses.create'));
    }

    /** @test */
    public function instructor_cannot_view_links_related_to_companies()
    {
        $response = $this
            ->actingAs($this->instructor)
            ->get(route('dashboard'));

        $response
            ->assertDontSee(route('companies.index'))
            ->assertDontSee(route('companies.create'));
    }

    /** @test */
    public function instructor_cannot_view_links_related_to_instructors()
    {
        $response = $this
            ->actingAs($this->instructor)
            ->get(route('dashboard'));

        $response
            ->assertDontSee(route('instructors.index'))
            ->assertDontSee(route('instructors.create'));
    }

    /** @test */
    public function instructor_cannot_view_links_related_to_coordinators()
    {
        $response = $this
            ->actingAs($this->instructor)->get(route('dashboard'));

        $response->assertDontSee(route('coordinators.index'))
                 ->assertDontSee(route('coordinators.create'));
    }

    /** @test */
    public function instructor_cannot_view_links_related_to_admins()
    {
        $response = $this
            ->actingAs($this->instructor)->get(route('dashboard'));

        $response->assertDontSee(route('admins.index'))
                 ->assertDontSee(route('admins.create'));
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
    public function novice_cannot_view_links_related_to_courses()
    {
        $response = $this
            ->actingAs($this->novice)
            ->get(route('dashboard'));

        $response
            ->assertDontSee(route('courses.index'))
            ->assertDontSee(route('courses.create'));
    }

    /** @test */
    public function novice_cannot_view_links_related_to_companies()
    {
        $response = $this
            ->actingAs($this->novice)
            ->get(route('dashboard'));

        $response
            ->assertDontSee(route('companies.index'))
            ->assertDontSee(route('companies.create'));
    }

    /** @test */
    public function novice_cannot_view_links_related_to_instructors()
    {
        $response = $this
            ->actingAs($this->novice)
            ->get(route('dashboard'));

        $response
            ->assertDontSee(route('instructors.index'))
            ->assertDontSee(route('instructors.create'));
    }

    /** @test */
    public function novice_cannot_view_links_related_to_coordinators()
    {
        $response = $this
            ->actingAs($this->novice)->get(route('dashboard'));

        $response->assertDontSee(route('coordinators.index'))
                 ->assertDontSee(route('coordinators.create'));
    }

    /** @test */
    public function novice_cannot_view_links_related_to_admins()
    {
        $response = $this
            ->actingAs($this->novice)->get(route('dashboard'));

        $response->assertDontSee(route('admins.index'))
                 ->assertDontSee(route('admins.create'));
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

    /** @test */
    public function employer_cannot_view_links_related_to_courses()
    {
        $response = $this
            ->actingAs($this->employer)
            ->get(route('dashboard'));

        $response
            ->assertDontSee(route('courses.index'))
            ->assertDontSee(route('courses.create'));
    }

    /** @test */
    public function employer_can_view_links_related_to_his_company()
    {
        $response = $this
            ->actingAs($this->employer)
            ->get(route('dashboard'));

        $response
            ->assertSee(route('companies.show', [
                'company' => $this->employer->registration->company
            ]))
            ->assertDontSeeText(route('companies.index'))
            ->assertDontSee(route('companies.create'));
    }

    /** @test */
    public function employer_cannot_view_links_related_to_instructors()
    {
        $response = $this
            ->actingAs($this->employer)
            ->get(route('dashboard'));

        $response
            ->assertDontSee(route('instructors.index'))
            ->assertDontSee(route('instructors.create'));
    }

    /** @test */
    public function employer_cannot_view_links_related_to_coordinators()
    {
        $response = $this
            ->actingAs($this->employer)->get(route('dashboard'));

        $response->assertDontSee(route('coordinators.index'))
                 ->assertDontSee(route('coordinators.create'));
    }

    /** @test */
    public function employer_cannot_view_links_related_to_admins()
    {
        $response = $this
            ->actingAs($this->employer)->get(route('dashboard'));

        $response->assertDontSee(route('admins.index'))
                 ->assertDontSee(route('admins.create'));
    }
}
