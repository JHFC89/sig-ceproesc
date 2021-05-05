<?php

namespace Tests\Feature\Http\Controllers;

use App\Facades\InvitationCode;
use App\Models\Invitation;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateComplementaryProfileTest extends TestCase
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
            'phone'     => '12 12345 6789',
            'address'   => [
                'street'    => 'Updated Fake Street',
                'number'    => '321',
                'district'  => 'Updated Fake Garden',
                'city'      => 'Updated Fake City',
                'state'     => 'Updated Fake State',
                'country'   => 'Updated Fake Country',
                'cep'       => '23.234-234',
            ],
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
    public function user_can_update_his_complementary_profile()
    {
        $data = $this->data;

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $data);

        $response->assertRedirect(route('profiles.show', ['user' => $this->user]))
                 ->assertSessionHas('status', 'Informações complementares atualizadas com sucesso!');

        $registration = $this->user->refresh()->registration;
        $address = $data['address'];
        $this->assertEquals($data['phone'], $registration->phones[0]->number);
        $this->assertEquals($address['street'], $registration->address->street);
        $this->assertEquals($address['number'], $registration->address->number);
        $this->assertEquals($address['district'], $registration->address->district);
        $this->assertEquals($address['city'], $registration->address->city);
        $this->assertEquals($address['state'], $registration->address->state);
        $this->assertEquals($address['country'], $registration->address->country);
        $this->assertEquals($address['cep'], $registration->address->cep);
    }

    /** @test */
    public function user_can_update_only_his_phone()
    {
        unset($this->data['address']);

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertRedirect(route('profiles.show', ['user' => $this->user]))
                 ->assertSessionHas('status', 'Informações complementares atualizadas com sucesso!');

        $registration = $this->user->refresh()->registration;
        $this->assertEquals($this->data['phone'], $registration->phones[0]->number);
    }

    /** @test */
    public function user_can_update_only_his_address()
    {
        unset($this->data['phone']);

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertRedirect(route('profiles.show', ['user' => $this->user]))
                 ->assertSessionHas('status', 'Informações complementares atualizadas com sucesso!');

        $registration = $this->user->refresh()->registration;
        $address = $this->data['address'];
        $this->assertEquals($address['street'], $registration->address->street);
        $this->assertEquals($address['number'], $registration->address->number);
        $this->assertEquals($address['district'], $registration->address->district);
        $this->assertEquals($address['city'], $registration->address->city);
        $this->assertEquals($address['state'], $registration->address->state);
        $this->assertEquals($address['country'], $registration->address->country);
        $this->assertEquals($address['cep'], $registration->address->cep);
    }


    /** @test */
    public function user_cannot_update_another_user_complementary_profile()
    {
        $anotherUser = User::factory()->create();

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $anotherUser
                         ]), $this->data);

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_update_an_user_complementary_profile()
    {
        $response = $this->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertRedirect('login');
    }

    /** @test */
    public function updated_phone_is_optional()
    {
        unset($this->data['phone']);

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasNoErrors('phone');
    }

    /** @test */
    public function updated_phone_is_required_if_present()
    {
        $this->data['phone'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('phone');
    }

    /** @test */
    public function updated_address_is_optional()
    {
        unset($this->data['address']);

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasNoErrors('address');
    }

    /** @test */
    public function updated_address_is_required_if_present()
    {
        $this->data['address'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('address');
    }

    /** @test */
    public function updated_address_street_is_required_if_present()
    {
        $this->data['address']['street'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('address.street');
    }

    /** @test */
    public function updated_address_number_is_required_if_present()
    {
        $this->data['address']['number'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('address.number');
    }

    /** @test */
    public function updated_address_district_is_required_if_present()
    {
        $this->data['address']['district'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('address.district');
    }

    /** @test */
    public function updated_address_city_is_required_if_present()
    {
        $this->data['address']['city'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('address.city');
    }

    /** @test */
    public function updated_address_state_is_required_if_present()
    {
        $this->data['address']['state'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('address.state');
    }

    /** @test */
    public function updated_address_country_is_required_if_present()
    {
        $this->data['address']['country'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('address.country');
    }

    /** @test */
    public function updated_address_cep_is_required_if_present()
    {
        $this->data['address']['cep'] = '';

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('address.cep');
    }

    /** @test */
    public function updated_address_cep_must_be_10_characters_long()
    {
        $this->data['address']['cep'] = '12.123-12';

        $response = $this->actingAs($this->user)
                         ->patch(route('complementary-profiles.update', [
                             'user' => $this->user
                         ]), $this->data);

        $response->assertSessionHasErrors('address.cep');
    }
}
