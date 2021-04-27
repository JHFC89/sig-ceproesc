<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Mail\InvitationEmail;
use App\Facades\InvitationCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Role, Company, Invitation, Registration};

class StoreNoviceTest extends TestCase
{
    use RefreshDatabase;

    protected $coordinator;

    protected $company;

    protected $data;

    protected $from;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::fakeCoordinator();

        Role::firstOrCreate(['name' => Role::NOVICE]);

        $this->company = Company::factory()->create();

        $this->data = [
            'name'              => 'Test Novice Name',
            'email'             => 'novice@test.com',
            'birthdate'         => [
                'day'   => 1,
                'month' => 1,
                'year'  => 2000,
            ],
            'rg'                => '12-123-123-2',
            'cpf'               => '123.123.123-12',
            'ctps'              => '12-1234-12',
            'phone'             => '1234567',
            'responsable_name'  => 'Test Novice Responsable Name',
            'responsable_cpf'   => '111.111.111-12',
            'address'           => [
                'street'    => 'Test Street',
                'number'    => '123',
                'district'  => 'Test Garden',
                'city'      => 'Test City',
                'state'     => 'Test State',
                'country'   => 'Test Country',
                'cep'       => '12.123-123',
            ]
        ];

        $this->from = route('companies.novices.create', [
            'company' => $this->company,
        ]);
    }

    private function validPostFor(User $user)
    {
        return $this->actingAs($user)
                    ->post(route('companies.novices.store', [
                        'company' => $this->company
                    ]), $this->data);
    }

    private function coordinatorPostDataFrom(string $from, array $data)
    {
        return $this->actingAs($this->coordinator)
                    ->from($from)
                    ->post(route('companies.novices.store', [
                        'company' => $this->company
                    ]), $data);
    }

    /** @test */
    public function coordinator_can_store_a_novice()
    {
        Mail::fake();
        InvitationCode::shouldReceive('generate')->andReturn('TESTCODE1234');

        $data = $this->data;

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.novices.store', [
                             'company' => $this->company
                         ]), $data);

        $response->assertOk()
                 ->assertViewIs('novices.show')
                 ->assertSessionHas('status', 'Aprendiz cadastrado com sucesso!');

        $registration = Registration::where('name', 'Test Novice Name')->first();
        $this->assertEquals($data['name'], $registration->name);
        $this->assertEquals('01/01/2000', $registration->formatted_birthdate);
        $this->assertEquals($data['rg'], $registration->rg);
        $this->assertEquals($data['cpf'], $registration->cpf);
        $this->assertEquals($data['ctps'], $registration->ctps);
        $this->assertEquals($data['phone'], $registration->phones[0]->number);
        $this->assertEquals($data['responsable_name'], $registration->responsable_name);
        $this->assertEquals($data['responsable_cpf'], $registration->responsable_cpf);
        $this->assertEquals($data['address']['street'], $registration->address->street);
        $this->assertEquals($data['address']['number'], $registration->address->number);
        $this->assertEquals($data['address']['district'], $registration->address->district);
        $this->assertEquals($data['address']['city'], $registration->address->city);
        $this->assertEquals($data['address']['state'], $registration->address->state);
        $this->assertEquals($data['address']['country'], $registration->address->country);
        $this->assertEquals($data['address']['cep'], $registration->address->cep);
        $this->assertTrue($registration->employer->is($this->company));
        $this->assertEquals(Role::NOVICE, $registration->role->name);

        $invitation = Invitation::where('email', 'novice@test.com')->first();
        $this->assertTrue($registration->invitation->is($invitation));
        $this->assertEquals('TESTCODE1234', $invitation->code);

        Mail::assertSent(InvitationEmail::class, function ($mail) use ($invitation) {
            return $mail->hasTo('novice@test.com')
                && $mail->invitation->is($invitation);
        });
    }

    /** @test */
    public function guest_cannot_store_a_novice()
    {
        $response = $this->post(route('companies.novices.store', [
            'company' => $this->company,
        ]), $this->data);

        $response->assertRedirect('login');
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function user_without_role_cannot_store_a_novice()
    {
        $user = User::factory()->create();

        $response = $this->validPostFor($user);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function instructor_cannot_store_a_novice()
    {
        $instructor = User::fakeInstructor();

        $response = $this->validPostFor($instructor);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function novice_cannot_store_a_novice()
    {
        $novice = User::fakeNovice();

        $response = $this->validPostFor($novice);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function employer_cannot_store_a_novice()
    {
        $employer = User::fakeEmployer();

        $response = $this->validPostFor($employer);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function name_is_required()
    {
        unset($this->data['name']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('name')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function email_is_required()
    {
        unset($this->data['email']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('email')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function email_must_be_email()
    {
        $this->data['email'] = 'not an email';

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('email')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function email_must_be_unique_in_users_table()
    {
        $existingUser = User::factory()->create(['email' => 'used@test.com']);
        $this->data['email'] = 'used@test.com';

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('email')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function email_must_be_unique_in_invitations_table()
    {
        $existingInvitation = Invitation::factory()->create([
            'email' => 'used@test.com'
        ]);
        $this->data['email'] = 'used@test.com';

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('email')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function birthdate_is_required()
    {
        unset($this->data['birthdate']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('birthdate')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function birthdate_day_is_required()
    {
        unset($this->data['birthdate']['day']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('birthdate.day')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function birthdate_day_must_be_a_integer()
    {
        $this->data['birthdate']['day'] = 'one';

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('birthdate.day')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function birthdate_month_is_required()
    {
        unset($this->data['birthdate']['month']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('birthdate.month')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function birthdate_month_must_be_a_integer()
    {
        $this->data['birthdate']['month'] = 'one';

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('birthdate.month')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function birthdate_year_is_required()
    {
        unset($this->data['birthdate']['year']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('birthdate.year')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function birthdate_year_must_be_a_integer()
    {
        $this->data['birthdate']['year'] = 'one';

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('birthdate.year')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function rg_is_required()
    {
        unset($this->data['rg']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('rg')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function rg_must_be_unique()
    {
        $existingRegistration = Registration::factory()->forInstructor()
                                                       ->create();
        $this->data['rg'] = $existingRegistration->rg;

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('rg')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function cpf_is_required()
    {
        unset($this->data['cpf']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('cpf')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function cpf_must_be_unique()
    {
        $existingRegistration = Registration::factory()->forInstructor()
                                                       ->create();
        $this->data['cpf'] = $existingRegistration->cpf;

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('cpf')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function cpf_must_be_14_characters_long()
    {
        $this->data['cpf'] = '123.123.123-1';

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('cpf')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function ctps_is_required()
    {
        unset($this->data['ctps']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('ctps')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function ctps_must_be_unique()
    {
        $existingRegistration = Registration::factory()->forInstructor()
                                                       ->create();
        $this->data['ctps'] = $existingRegistration->ctps;

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('ctps')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function responsable_name_is_required()
    {
        unset($this->data['responsable_name']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('responsable_name')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function responsable_cpf_is_required()
    {
        unset($this->data['responsable_cpf']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('responsable_cpf')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function responsable_cpf_must_be_14_characters_long()
    {
        $this->data['responsable_cpf'] = '111.111.111-1';

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('responsable_cpf')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function phone_is_required()
    {
        unset($this->data['phone']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('phone')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function address_is_required()
    {
        unset($this->data['address']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('address')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function street_is_required()
    {
        unset($this->data['address']['street']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('address.street')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function number_is_required()
    {
        unset($this->data['address']['number']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('address.number')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function district_is_required()
    {
        unset($this->data['address']['district']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('address.district')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function city_is_required()
    {
        unset($this->data['address']['city']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('address.city')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function state_is_required()
    {
        unset($this->data['address']['state']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('address.state')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function country_is_required()
    {
        unset($this->data['address']['country']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('address.country')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function cep_is_required()
    {
        unset($this->data['address']['cep']);

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('address.cep')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function cep_must_be_10_characters_long()
    {
        $this->data['address']['cep'] = '12.123-12';

        $response = $this->coordinatorPostDataFrom($this->from, $this->data);

        $response->assertSessionHasErrors('address.cep')
                 ->assertRedirect($this->from);
    }
}
