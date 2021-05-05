<?php

namespace Tests\Feature\Http\Controllers;

use App\Facades\InvitationCode;
use App\Models\Invitation;
use App\Models\Registration;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewProfileTest extends TestCase
{
    use RefreshDatabase;

    private function fakePhone()
    {
        return ['number' => '16 97897 9877'];
    }

    private function fakeAddress()
    {
        return [
            'street'    => 'Fake Street',
            'number'    => '123',
            'district'  => 'Fake Garden',
            'city'      => 'Fake City',
            'state'     => 'Fake State',
            'country'   => 'Fake Country',
            'cep'       => '12.123-123',
        ];
    }

    /** @test */
    public function user_can_view_his_profile()
    {
        $registration = Registration::factory()->forInstructor()->create();
        $registration->phones()->create($this->fakePhone());
        $registration->address()->create($this->fakeAddress());
        $invitation = $registration->invitation()->save(new Invitation([
            'email' => 'user@test.com',
            'code' => InvitationCode::generate(),
        ]));
        $user = $invitation->createUserFromArray(['password' => 'password']);

        $response = $this->actingAs($user)
                         ->get(route('profiles.show', ['user' => $user]));

        $response->assertOk()
                 ->assertViewIs('profiles.show')
                 ->assertViewHas('user');
    }

    /** @test */
    public function user_cannot_view_another_user_profile()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('profiles.show', ['user' => $anotherUser]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_view_an_user_profile()
    {
        $user = User::factory()->create();

        $response = $this->get(route('profiles.show', ['user' => $user]));

        $response->assertRedirect('login');
    }
}
