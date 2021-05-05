<?php

namespace Tests\Feature\Http\Controllers;

use App\Facades\InvitationCode;
use App\Models\Invitation;
use App\Models\Registration;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateProfessionalProfileTest extends TestCase
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
            'code'  => InvitationCode::generate(),
        ]));
        $this->user = $invitation->createUserFromArray([
            'password' => 'password'
        ]);

        $this->data = [
            'ctps' => '123456789',
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
    public function user_can_update_his_professional_profile()
    {
        $data = $this->data;

        $response = $this->actingAs($this->user)
                         ->patch(route('professional-profiles.update', [
                             'user' => $this->user
                         ]), $data);

        $response->assertRedirect(route('profiles.show', ['user' => $this->user]))
                 ->assertSessionHas('status', 'Informações profissionais atualizadas com sucesso!');

        $this->assertEquals(
            $data['ctps'],
            $this->user->refresh()->registration->ctps
        );
    }

    /** @test */
    public function user_cannot_update_another_user_professional_profile()
    {
        $anotherUser = User::factory()->create();

        $response = $this->actingAs($this->user)
                         ->patch(route('professional-profiles.update', [
                             'user' => $anotherUser
                         ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_update_an_user_professional_profile()
    {
        $response = $this->patch(route('professional-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertRedirect('login');
    }

    /** @test */
    public function updated_ctps_is_required()
    {
        unset($this->data['ctps']);

        $response = $this->actingAs($this->user)
                         ->patch(route('professional-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('ctps');
    }

    /** @test */
    public function updated_ctps_must_not_be_empty()
    {
        $this->data['ctps'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('professional-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('ctps');
    }
}
