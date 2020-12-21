<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create(['api_token' => null]);
        $this->assertNull($user->api_token);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
        $this->assertNotNull($user->fresh()->api_token);
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create(['api_token' => null]);
        $this->assertNull($user->api_token);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $this->assertNull($user->api_token);
    }

    /** @test */
    public function user_api_token_is_set_to_null_when_logout()
    {
        $user = User::factory()->create();
        $this->assertNotNull($user->api_token);

        $this->actingAs($user)->post('logout');

        $this->assertGuest();
        $this->assertNull($user->fresh()->api_token);
    }
}
