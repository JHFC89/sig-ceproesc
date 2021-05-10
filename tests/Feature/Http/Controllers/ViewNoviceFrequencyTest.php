<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CourseClass;
use App\Models\Registration;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewNoviceFrequencyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_view_the_frequency_of_a_novice()
    {
        $novice = User::fakeNovice();
        $registration = Registration::factory()->forNovice()->create();
        $registration->user()->associate($novice)->save();
        $courseClass = CourseClass::factory()->create();
        $courseClass->subscribe($novice);
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)
                         ->get(route('novices.frequencies.show', [
                             'registration' => $registration->id
                         ]));
        
        $response->assertOk();
    }

    /** @test */
    public function cannot_view_the_frequency_of_a_novice_not_subscribed_to_a_course_class()
    {
        $novice = User::fakeNovice();
        $registration = Registration::factory()->forNovice()->create();
        $registration->user()->associate($novice)->save();
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)
                         ->get(route('novices.frequencies.show', [
                             'registration' => $registration->id
                         ]));
        
        $response->assertNotFound();
    }

    /** @test */
    public function cannot_view_the_frequency_of_a_user_that_is_not_a_novice()
    {
        $notNovice = User::fakeInstructor();
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)
                         ->get(route('novices.frequencies.show', [
                             'registration' => $notNovice->registration->id
                         ]));
        
        $response->assertNotFound();
    }

    /** @test */
    public function guest_cannot_view_the_frequency_of_a_novice()
    {
        $novice = User::fakeNovice();

        $response = $this->get(route('novices.frequencies.show', [
                             'registration' => $novice->registration->id
                         ]));
        
        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_view_the_frequency_of_a_novice()
    {
        $novice = User::fakeNovice();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('novices.frequencies.show', [
                             'registration' => $novice->registration->id
                         ]));
        
        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_view_a_novice_frequency_of_another_employer()
    {
        $novice = User::fakeNovice();
        $courseClass = CourseClass::factory()->create();
        $courseClass->subscribe($novice);
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('novices.frequencies.show', [
                             'registration' => $novice->registration->id
                         ]));
        
        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_view_the_frequency_of_another_novice()
    {
        $anotherNovice = User::fakeNovice();
        $registration = Registration::factory()->forNovice()->create();
        $registration->user()->associate($anotherNovice)->save();
        $courseClass = CourseClass::factory()->create();
        $courseClass->subscribe($anotherNovice);
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('novices.frequencies.show', [
                             'registration' => $registration->id
                         ]));
        
        $response->assertUnauthorized();
    }
}
