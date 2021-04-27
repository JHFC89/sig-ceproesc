<?php

namespace Tests\Feature;

use App\Models\{Company, Invitation, Registration, Role, User};
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcceptInvitationTest extends TestCase
{
    use RefreshDatabase;

    private function validInvitationForEmployer(string $code = 'TESTCODE1234')
    {
        return Invitation::factory()->create([
            'user_id'           => null,
            'code'              => $code,
            'registration_id'   => Registration::factory()->create([
                'name' => 'Test User Name'
            ]),
        ]);
    }

    /** @test */
    public function viewing_an_unused_invitation()
    {
        $invitation = Invitation::factory()->create([
            'user_id'   => null,
            'code'      => 'TESTCODE1234'
        ]);

        $response = $this->get(route('invitations.show', [
            'code'      => 'TESTCODE1234',
        ]));

        $response->assertOk()
                 ->assertViewIs('invitations.show')
                 ->assertViewHas('invitation');
    }

    /** @test */
    public function viewing_an_used_invitation()
    {
        $invitation = Invitation::factory()->create([
            'user_id'   => User::factory()->create(),
            'code'      => 'TESTCODE1234'
        ]);

        $response = $this->get(route('invitations.show', [
            'code' => 'TESTCODE1234',
        ]));

        $response->assertNotFound();
    }

    /** @test */
    public function viewing_an_invitation_that_does_not_exist()
    {
        $response = $this->get(route('invitations.show', [
            'code' => 'TESTCODE1234',
        ]));

        $response->assertNotFound();
    }

    /** @test */
    public function employer_registering_with_a_valid_registration_code()
    {
        $registration = Registration::factory()->create([
            'name'          => 'Test User Name',
            'rg'            => '123-123-12',
            'role_id'       => Role::factory()->create(['name' => 'employer']),
            'company_id'    => Company::factory()->create(),
        ]);
        $invitation = Invitation::factory()->make([
            'email'             => 'test@test.com',
            'user_id'           => null,
            'code'              => 'TESTCODE1234',
        ]);
        $registration->invitation()->save($invitation);
        $data = [
            'email'                 => 'test@test.com',
            'password'              => 'Secret1',
            'password_confirmation' => 'Secret1',
            'confirmation_code'     => 'TESTCODE1234',
        ];

        $response = $this->post(route('register.store', $data));

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals(1, User::count());
        $user = User::first();
        $this->assertEquals('Test User Name', $user->name);
        $this->assertEquals('test@test.com', $user->email);
        $this->assertEquals('123-123-12', $user->registration->rg);
        $this->assertTrue(Hash::check('Secret1', $user->password));
        $this->assertTrue($invitation->fresh()->user->is($user));
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->isEmployer());
        $this->assertTrue($user->company->is($registration->company));
    }

    /** @test */
    public function instructor_registering_with_a_valid_registration_code()
    {
        $registration = Registration::factory()->create([
            'name'      => 'Fake Instructor',
            'role_id'   => Role::factory()->create(['name' => 'instructor']),
            'rg'        => '12-123-123',
            'cpf'       => '123.123.123-12',
            'ctps'      => '12-123-12',
        ]);
        $invitation = Invitation::factory()->make([
            'email'             => 'fakeinstructor@test.com',
            'user_id'           => null,
            'code'              => 'TESTCODE1234',
        ]);
        $registration->invitation()->save($invitation);
        $data = [
            'email'                 => 'fakeinstructor@test.com',
            'password'              => 'Secret1',
            'password_confirmation' => 'Secret1',
            'confirmation_code'     => 'TESTCODE1234',
        ];

        $response = $this->post(route('register.store', $data));

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals(1, User::count());
        $user = User::first();
        $this->assertEquals($registration->name, $user->name);
        $this->assertEquals($registration->email, $user->email);
        $this->assertEquals($registration->rg, $user->registration->rg);
        $this->assertEquals($registration->cpf, $user->registration->cpf);
        $this->assertEquals($registration->ctps, $user->registration->ctps);
        $this->assertTrue(Hash::check('Secret1', $user->password));
        $this->assertTrue($invitation->fresh()->user->is($user));
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->isInstructor());
    }

    /** @test */
    public function novice_registering_with_a_valid_registration_code()
    {
        $employer = Company::factory()->create();
        $registration = Registration::factory()->forNovice($employer->id)
                                               ->create([
            'name'          => 'Fake Novice',
        ]);
        $invitation = Invitation::factory()->make([
            'email'             => 'fakenovice@test.com',
            'user_id'           => null,
            'code'              => 'TESTCODE1234',
        ]);
        $registration->invitation()->save($invitation);
        $data = [
            'email'                 => 'fakenovice@test.com',
            'password'              => 'Secret1',
            'password_confirmation' => 'Secret1',
            'confirmation_code'     => 'TESTCODE1234',
        ];

        $response = $this->post(route('register.store', $data));

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals(1, User::count());
        $user = User::first();
        $this->assertEquals('Fake Novice', $user->name);
        $this->assertEquals('fakenovice@test.com', $user->email);
        $this->assertTrue(Hash::check('Secret1', $user->password));
        $this->assertTrue($invitation->fresh()->user->is($user));
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->isNovice());
        $this->assertTrue($user->employer->is($employer));
    }

    /** @test */
    public function registering_with_a_used_registration_code()
    {
        $invitation = Invitation::factory()->create([
            'user_id'   => User::factory()->create(),
            'code'      => 'TESTCODE1234'
        ]);
        $this->assertEquals(1, User::count());
        $data = [
            'email'                 => 'test@test.com',
            'password'              => 'Secret1',
            'password_confirmation' => 'Secret1',
            'confirmation_code'     => 'TESTCODE1234',
        ];

        $response = $this->post(route('register.store', $data));

        $response->assertNotFound();
        $this->assertEquals(1, User::count());
    }

    /** @test */
    public function registering_with_a_registration_code_that_does_not_exist()
    {
        $data = [
            'email'                 => 'test@test.com',
            'password'              => 'Secret1',
            'password_confirmation' => 'Secret1',
            'confirmation_code'     => 'TESTCODE1234',
        ];

        $response = $this->post(route('register.store', $data));

        $response->assertNotFound();
        $this->assertEquals(0, User::count());
    }

    /** @test */
    public function email_is_required()
    {
        $invitation = $this->validInvitationForEmployer('TESTCODE1234');
        $data = [
            'password'              => 'Secret1',
            'password_confirmation' => 'Secret1',
            'confirmation_code'     => 'TESTCODE1234',
        ];
        $from = $this->from(route('invitations.show', [
            'code' => 'TESTCODE1234',
        ]));

        $response = $from->post(route('register.store', $data));

        $response->assertRedirect(route('invitations.show', [
            'code' => 'TESTCODE1234'
        ]));
        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, User::count());
    }

    /** @test */
    public function email_must_be_email()
    {
        $invitation = $this->validInvitationForEmployer('TESTCODE1234');
        $data = [
            'email'                 => 'not_an_email',
            'password'              => 'Secret1',
            'password_confirmation' => 'Secret1',
            'confirmation_code'     => 'TESTCODE1234',
        ];
        $from = $this->from(route('invitations.show', [
            'code' => 'TESTCODE1234',
        ]));

        $response = $from->post(route('register.store', $data));

        $response->assertRedirect(route('invitations.show', [
            'code' => 'TESTCODE1234'
        ]));
        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, User::count());
    }

    /** @test */
    public function email_must_be_the_same_from_invitation()
    {
        $invitation = $this->validInvitationForEmployer('TESTCODE1234');
        $invitation->update(['email' => 'test@test.com']);
        $data = [
            'email'                 => 'not_same_email_from_invitation@test.com',
            'password'              => 'Secret1',
            'password_confirmation' => 'Secret1',
            'confirmation_code'     => 'TESTCODE1234',
        ];
        $from = $this->from(route('invitations.show', [
            'code' => 'TESTCODE1234',
        ]));

        $response = $from->post(route('register.store', $data));

        $response->assertRedirect(route('invitations.show', [
            'code' => 'TESTCODE1234'
        ]));
        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, User::count());
    }

    /** @test */
    public function email_must_be_unique()
    {
        $existingUser = User::factory()->create(['email' => 'user@test.com']);
        $this->assertEquals(1, User::count());
        $invitation = $this->validInvitationForEmployer('TESTCODE1234');
        $invitation->update(['email' => 'user@test.com']);
        $data = [
            'email'                 => 'user@test.com',
            'password'              => 'Secret1',
            'password_confirmation' => 'Secret1',
            'confirmation_code'     => 'TESTCODE1234',
        ];
        $from = $this->from(route('invitations.show', [
            'code' => 'TESTCODE1234',
        ]));

        $response = $from->post(route('register.store', $data));

        $response->assertRedirect(route('invitations.show', [
            'code' => 'TESTCODE1234'
        ]));
        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, User::count());
    }

    /** @test */
    public function password_is_required()
    {
        $invitation = $this->validInvitationForEmployer('TESTCODE1234');
        $data = [
            'email'                 => $invitation->email,
            'password_confirmation' => 'Secret1',
            'confirmation_code'     => 'TESTCODE1234',
        ];
        $from = $this->from(route('invitations.show', [
            'code' => 'TESTCODE1234',
        ]));

        $response = $from->post(route('register.store', $data));

        $response->assertRedirect(route('invitations.show', [
            'code' => 'TESTCODE1234'
        ]));
        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, User::count());
    }

    /** @test */
    public function password_must_have_a_mininum_of_6_characters()
    {
        $invitation = $this->validInvitationForEmployer('TESTCODE1234');
        $data = [
            'email'                 => $invitation->email,
            'password'              => 'secre',
            'password_confirmation' => 'secre',
            'confirmation_code'     => 'TESTCODE1234',
        ];
        $from = $this->from(route('invitations.show', [
            'code' => 'TESTCODE1234',
        ]));

        $response = $from->post(route('register.store', $data));

        $response->assertRedirect(route('invitations.show', [
            'code' => 'TESTCODE1234'
        ]));
        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, User::count());
    }

    /** @test */
    public function password_must_have_at_least_one_lowercase_character()
    {
        $invitation = $this->validInvitationForEmployer('TESTCODE1234');
        $data = [
            'email'                 => $invitation->email,
            'password'              => 'SECRET1',
            'password_confirmation' => 'SECRET1',
            'confirmation_code'     => 'TESTCODE1234',
        ];
        $from = $this->from(route('invitations.show', [
            'code' => 'TESTCODE1234',
        ]));

        $response = $from->post(route('register.store', $data));

        $response->assertRedirect(route('invitations.show', [
            'code' => 'TESTCODE1234'
        ]));
        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, User::count());
    }

    /** @test */
    public function password_must_have_at_least_one_uppercase_character()
    {
        $invitation = $this->validInvitationForEmployer('TESTCODE1234');
        $data = [
            'email'                 => $invitation->email,
            'password'              => 'secret1',
            'password_confirmation' => 'secret1',
            'confirmation_code'     => 'TESTCODE1234',
        ];
        $from = $this->from(route('invitations.show', [
            'code' => 'TESTCODE1234',
        ]));

        $response = $from->post(route('register.store', $data));

        $response->assertRedirect(route('invitations.show', [
            'code' => 'TESTCODE1234'
        ]));
        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, User::count());
    }

    /** @test */
    public function password_must_have_at_least_one_number_character()
    {
        $invitation = $this->validInvitationForEmployer('TESTCODE1234');
        $data = [
            'email'                 => $invitation->email,
            'password'              => 'Secret',
            'password_confirmation' => 'Secret',
            'confirmation_code'     => 'TESTCODE1234',
        ];
        $from = $this->from(route('invitations.show', [
            'code' => 'TESTCODE1234',
        ]));

        $response = $from->post(route('register.store', $data));

        $response->assertRedirect(route('invitations.show', [
            'code' => 'TESTCODE1234'
        ]));
        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, User::count());
    }

    /** @test */
    public function password_must_be_confirmed()
    {
        $invitation = $this->validInvitationForEmployer('TESTCODE1234');
        $data = [
            'email'             => $invitation->email,
            'password'          => 'Secret1',
            'confirmation_code' => 'TESTCODE1234',
        ];
        $from = $this->from(route('invitations.show', [
            'code' => 'TESTCODE1234',
        ]));

        $response = $from->post(route('register.store', $data));

        $response->assertRedirect(route('invitations.show', [
            'code' => 'TESTCODE1234'
        ]));
        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, User::count());
    }
}
