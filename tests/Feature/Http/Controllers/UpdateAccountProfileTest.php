<?php

namespace Tests\Feature\Http\Controllers;

use App\Facades\InvitationCode;
use App\Models\Invitation;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateAccountProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $data;

    protected function setUp():void
    {
        parent::setUp();

        $registration = Registration::factory()->forInstructor()->create();
        $registration->phones()->create($this->fakePhone());
        $registration->address()->create($this->fakeAddress());
        $invitation = $registration->invitation()->save(new Invitation([
            'email' => 'user@test.com',
            'code' => InvitationCode::generate(),
        ]));
        $this->user = $invitation->createUserFromArray([
            'password' => 'password'
        ]);

        $this->data = [
            'email'                 => 'updatedemail@test.com',
            'password'              => 'UpdatedPass1',
            'password_confirmation' => 'UpdatedPass1',
            'current_password'      => 'password',
        ];
    }

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
    public function user_can_update_his_account_profile()
    {
        $data = [
            'email'                 => 'updatedemail@test.com',
            'password'              => 'UpdatedPass1',
            'password_confirmation' => 'UpdatedPass1',
            'current_password'      => 'password',
        ];

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $data);

        $response->assertRedirect(route('profiles.show', ['user' => $this->user]))
                 ->assertSessionHas('status', 'Informações da conta atualizadas com sucesso!');

        $this->user->refresh();
        $this->assertEquals($data['email'], $this->user->email);
        $this->assertTrue(Hash::check($data['password'], $this->user->password));
    }

    /** @test */
    public function user_can_update_only_his_password()
    {
        $this->data['email'] = $this->user->email;

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertRedirect(route('profiles.show', ['user' => $this->user]))
                 ->assertSessionHas('status', 'Informações da conta atualizadas com sucesso!');

        $this->user->refresh();
        $this->assertEquals($this->data['email'], $this->user->email);
        $this->assertTrue(Hash::check($this->data['password'], $this->user->password));
    }

    /** @test */
    public function user_cannot_update_another_user_account_profile()
    {
        $anotherUser = User::factory()->create();

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $anotherUser
                         ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_update_an_user_account_profile()
    {
        $response = $this->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertRedirect('login');
    }

    /** @test */
    public function updated_email_is_required()
    {
        unset($this->data['email']);

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function updated_email_must_be_unique()
    {
        $existingUser = User::factory()->create();
        $this->data['email'] = $existingUser->email;

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function updated_email_must_be_email()
    {
        $this->data['email'] = 'not an email';

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function updated_password_is_optional()
    {
        $this->data['password'] = '';
        $this->data['password_confirmation'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertRedirect(route('profiles.show', ['user' => $this->user]));
        $this->assertTrue(Hash::check('password', $this->user->password));
    }

    /** @test */
    public function updated_password_must_have_a_mininum_of_6_characters()
    {
        $this->data['password'] = 'Not6c';
        $this->data['password_confirmation'] = $this->data['password'];

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function updated_password_must_have_at_least_one_lowercase_character()
    {
        $this->data['password'] = 'NOLOWERCASE1';
        $this->data['password_confirmation'] = $this->data['password'];

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function updated_password_must_have_at_least_one_uppercase_character()
    {
        $this->data['password'] = 'nouppercase1';
        $this->data['password_confirmation'] = $this->data['password'];

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function updated_password_must_have_at_least_one_number_character()
    {
        $this->data['password'] = 'NoNumbers';
        $this->data['password_confirmation'] = $this->data['password'];

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function updated_password_must_be_confirmed()
    {
        $this->data['password_confirmation'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function current_password_must_be_correct()
    {
        $this->data['current_password'] = 'wrong-password1';

        $response = $this->actingAs($this->user)
                         ->patch(route('account-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('current_password');
    }
}
