<?php

namespace Tests\Feature\Http\Controllers;

use App\Facades\InvitationCode;
use App\Models\Invitation;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdatePersonalProfileTest extends TestCase
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
            'name'              => 'Fake User Name',
            'birthdate'         => [
                'day'   => 2,
                'month' => 2,
                'year'  => 1999,
            ],
            'rg'                => '123456789',
            'cpf'               => '123.456.789-10',
            'responsable_name'  => 'password',
            'responsable_cpf'   => '234.567.890-11',
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
    public function user_can_update_his_personal_profile()
    {
        $data = $this->data;

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $data);

        $response->assertRedirect(route('profiles.show', ['user' => $this->user]))
                 ->assertSessionHas('status', 'Informações pessoais atualizadas com sucesso!');

        $this->user->refresh();
        $this->assertEquals($data['name'], $this->user->name);
        $this->assertEquals('02/02/1999', $this->user->registration->formatted_birthdate);
        $this->assertEquals($data['rg'], $this->user->registration->rg);
        $this->assertEquals($data['cpf'], $this->user->registration->cpf);
        $this->assertEquals($data['responsable_name'], $this->user->registration->responsable_name);
        $this->assertEquals($data['responsable_cpf'], $this->user->registration->responsable_cpf);
    }

    /** @test */
    public function user_cannot_update_another_user_personal_profile()
    {
        $anotherUser = User::factory()->create();

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $anotherUser
                         ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_update_an_user_personal_profile()
    {
        $response = $this->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertRedirect('login');
    }

    /** @test */
    public function updated_name_is_required()
    {
        unset($this->data['name']);

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function updated_birthdate_is_optional()
    {
        unset($this->data['birthdate']);

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasNoErrors('birthdate');
    }

    /** @test */
    public function updated_birthdate_is_required_if_present()
    {
        $this->data['birthdate'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('birthdate');
    }

    /** @test */
    public function updated_birthdate_day_is_required_if_present()
    {
        $this->data['birthdate']['day'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('birthdate.day');
    }

    /** @test */
    public function updated_birthdate_day_must_be_a_integer()
    {
        $this->data['birthdate']['day'] = 'one';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('birthdate.day');
    }

    /** @test */
    public function updated_birthdate_month_is_required_if_present()
    {
        $this->data['birthdate']['month'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('birthdate.month');
    }

    /** @test */
    public function updated_birthdate_month_must_be_a_integer()
    {
        $this->data['birthdate']['month'] = 'one';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('birthdate.month');
    }

    /** @test */
    public function updated_birthdate_year_is_required_if_present()
    {
        $this->data['birthdate']['year'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('birthdate.year');
    }

    /** @test */
    public function updated_birthdate_year_must_be_a_integer()
    {
        $this->data['birthdate']['year'] = 'one';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('birthdate.year');
    }

    /** @test */
    public function updated_rg_is_optional()
    {
        unset($this->data['rg']);

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasNoErrors('rg');
    }

    /** @test */
    public function updated_rg_is_required_if_present()
    {
        $this->data['rg'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('rg');
    }

    /** @test */
    public function updated_rg_must_be_unique()
    {
        $existingRegistration = Registration::factory()->forInstructor()
                                                       ->create();
        $this->data['rg'] = $existingRegistration->rg;

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('rg');
    }

    /** @test */
    public function updated_cpf_is_optional()
    {
        unset($this->data['cpf']);

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasNoErrors('cpf');
    }

    /** @test */
    public function updated_cpf_is_required_if_present()
    {
        $this->data['cpf'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('cpf');
    }

    /** @test */
    public function updated_cpf_must_be_unique()
    {
        $existingRegistration = Registration::factory()->forInstructor()
                                                       ->create();
        $this->data['cpf'] = $existingRegistration->cpf;

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('cpf');
    }

    /** @test */
    public function updated_cpf_must_be_14_characters_long()
    {
        $this->data['cpf'] = '123.123.123-1';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('cpf');
    }

    /** @test */
    public function updated_responsable_name_is_optional()
    {
        unset($this->data['responsable_name']);

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasNoErrors('responsable_name');
    }

    public function updated_responsable_name_is_required_if_present()
    {
        $this->data['responsable_name'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('responsable_name');
    }

    /** @test */
    public function updated_responsable_cpf_is_optional()
    {
        unset($this->data['responsable_cpf']);

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasNoErrors('responsable_cpf');
    }

    /** @test */
    public function updated_responsable_cpf_is_required_if_present()
    {
        $this->data['responsable_cpf'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('responsable_cpf');
    }

    /** @test */
    public function updated_responsable_cpf_must_be_14_characters_long()
    {
        $this->data['responsable_cpf'] = '111.111.111-1';

        $response = $this->actingAs($this->user)
                         ->patch(route('personal-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('responsable_cpf');
    }
}
