<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreActivatedUserTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $inactiveUser;

    protected $data;

    protected function setUp():void
    {
        parent::setUp();

        $this->admin = User::fakeAdmin();

        $this->inactiveUser = User::factory()->inactive()->create();

        $this->data = [
            'user_id' => $this->inactiveUser->id,
        ];
    }

    /** @test */
    public function admin_can_store_an_activated_user()
    {
        $inactiveUser = User::factory()->inactive()->create();
        $data = [
            'user_id' => $inactiveUser->id,
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('activated-users.store'), $data);

        $response->assertRedirect()
                 ->assertSessionHas('status', 'Usuário ativado com sucesso!');
        $this->assertTrue($inactiveUser->refresh()->active);
    }

    /** @test */
    public function cannot_store_an_activated_user_that_is_currently_active()
    {
        $activeUser = User::factory()->create();
        $data = [
            'user_id' => $activeUser->id,
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('activated-users.store'), $data);

        $response->assertRedirect()
                 ->assertSessionHas('status', 'Usuário já está ativo!');
        $this->assertTrue($activeUser->refresh()->active);
    }

    /** @test */
    public function guest_cannot_store_an_activated_user()
    {
        $response = $this->post(route('activated-users.store'), $this->data);

        $response->assertRedirect('login');
        $this->assertFalse($this->inactiveUser->refresh()->active);
    }

    /** @test */
    public function user_without_role_cannot_store_an_activated_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->post(route('activated-users.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertFalse($this->inactiveUser->refresh()->active);
    }

    /** @test */
    public function coordinator_cannot_store_an_activated_user()
    {
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)
                         ->post(route('activated-users.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertFalse($this->inactiveUser->refresh()->active);
    }

    /** @test */
    public function instructor_cannot_store_an_activated_user()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->post(route('activated-users.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertFalse($this->inactiveUser->refresh()->active);
    }

    /** @test */
    public function novice_cannot_store_an_activated_user()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->post(route('activated-users.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertFalse($this->inactiveUser->refresh()->active);
    }

    /** @test */
    public function employer_cannot_store_an_activated_user()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->post(route('activated-users.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertFalse($this->inactiveUser->refresh()->active);
    }

    /** @test */
    public function user_id_is_required()
    {
        unset($this->data['user_id']);

        $response = $this->actingAs($this->admin)
                         ->post(route('activated-users.store'), $this->data);

        $response->assertSessionHasErrors('user_id');
        $this->assertFalse($this->inactiveUser->refresh()->active);
    }

    /** @test */
    public function user_must_exist()
    {
        $this->data['user_id'] = 1234;

        $response = $this->actingAs($this->admin)
                         ->post(route('activated-users.store'), $this->data);

        $response->assertSessionHasErrors('user_id');
        $this->assertFalse($this->inactiveUser->refresh()->active);
    }
}
