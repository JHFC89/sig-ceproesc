<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyActivatedUserTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $activeUser;

    protected $data;

    protected function setUp():void
    {
        parent::setUp();

        $this->admin = User::fakeAdmin();

        $this->activeUser = User::factory()->create();
    }

    /** @test */
    public function admin_can_destroy_an_activated_user()
    {
        $activeUser = User::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->delete(route('activated-users.destroy', [
                             'user' => $activeUser->id,
                         ]));

        $response->assertRedirect()
                 ->assertSessionHas('status', 'Usuário desativado com sucesso!');
        $this->assertFalse($activeUser->refresh()->active);
    }

    /** @test */
    public function cannot_destroy_an_activated_user_that_is_currently_inactive()
    {
        $inactiveUser = User::factory()->inactive()->create();

        $response = $this->actingAs($this->admin)
                         ->delete(route('activated-users.destroy', [
                             'user' => $inactiveUser->id,
                         ]));

        $response->assertRedirect()
                 ->assertSessionHas('status', 'Usuário já está desativado!');
        $this->assertFalse($inactiveUser->refresh()->active);
    }

    /** @test */
    public function guest_cannot_destroy_an_activated_user()
    {
        $response = $this->delete(route('activated-users.destroy', [
            'user' => $this->activeUser->id,
        ]));

        $response->assertRedirect('login');
        $this->assertTrue($this->activeUser->refresh()->active);
    }

    /** @test */
    public function user_without_role_cannot_destroy_an_activated_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->delete(route('activated-users.destroy', [
                             'user' => $this->activeUser->id,
                         ]));

        $response->assertUnauthorized();
        $this->assertTrue($this->activeUser->refresh()->active);
    }

    /** @test */
    public function coordinator_cannot_destroy_an_activated_user()
    {
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)
                         ->delete(route('activated-users.destroy', [
                             'user' => $this->activeUser->id,
                         ]));

        $response->assertUnauthorized();
        $this->assertTrue($this->activeUser->refresh()->active);
    }

    /** @test */
    public function instructor_cannot_destroy_an_activated_user()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->delete(route('activated-users.destroy', [
                             'user' => $this->activeUser->id,
                         ]));

        $response->assertUnauthorized();
        $this->assertTrue($this->activeUser->refresh()->active);
    }

    /** @test */
    public function novice_cannot_destroy_an_activated_user()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->delete(route('activated-users.destroy', [
                             'user' => $this->activeUser->id,
                         ]));

        $response->assertUnauthorized();
        $this->assertTrue($this->activeUser->refresh()->active);
    }

    /** @test */
    public function employer_cannot_destroy_an_activated_user()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->delete(route('activated-users.destroy', [
                             'user' => $this->activeUser->id,
                         ]));

        $response->assertUnauthorized();
        $this->assertTrue($this->activeUser->refresh()->active);
    }

    /** @test */
    public function user_must_exist()
    {
        $nonExistingUserId = 1234;

        $response = $this->actingAs($this->admin)
                         ->delete(route('activated-users.destroy', [
                             'user' => $nonExistingUserId,
                         ]));

        $response->assertNotFound();
    }
}
