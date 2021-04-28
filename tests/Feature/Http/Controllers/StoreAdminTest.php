<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Mail\InvitationEmail;
use App\Facades\InvitationCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Role, Invitation, Registration};

class StoreAdminTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $data;

    protected $from;

    protected function setUp():void
    {
        parent::setUp();

        $this->admin = User::fakeAdmin();

        $this->data = [
            'name'      => 'Test Admin Name',
            'email'     => 'admin@test.com',
            'phone'     => '1234567',
        ];

        $this->from = route('admins.create');
    }

    /** @test */
    public function admin_can_store_an_admin()
    {
        Mail::fake();
        InvitationCode::shouldReceive('generate')->andReturn('TESTCODE1234');

        $data = $this->data;

        $response = $this->actingAs($this->admin)
                         ->post(route('admins.store'), $data);

        $response->assertOk()
                 ->assertViewHas('registration')
                 ->assertViewIs('admins.show')
                 ->assertSessionHas('status', 'Administrador cadastrado com sucesso!');

        $registration = Registration::where('name', 'Test Admin Name')->first();
        $this->assertEquals($data['name'], $registration->name);
        $this->assertEquals(Role::ADMIN, $registration->role->name);

        $invitation = Invitation::where('email', 'admin@test.com')->first();
        $this->assertTrue($registration->invitation->is($invitation));
        $this->assertEquals('TESTCODE1234', $invitation->code);

        Mail::assertSent(InvitationEmail::class, function ($mail) use ($invitation) {
            return $mail->hasTo('admin@test.com')
                && $mail->invitation->is($invitation);
        });
    }

    /** @test */
    public function guest_cannot_store_an_admin()
    {
        $response = $this->post(route('admins.store'), $this->data);

        $response->assertRedirect('login');
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function user_without_role_cannot_store_an_admin()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->post(route('admins.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function coordinator_cannot_store_an_admin()
    {
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)
                         ->post(route('admins.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function instructor_cannot_store_an_admin()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->post(route('admins.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function novice_cannot_store_an_admin()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->post(route('admins.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function employer_cannot_store_an_admin()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->post(route('admins.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function name_is_required()
    {
        unset($this->data['name']);

        $response = $this->actingAs($this->admin)
                         ->from($this->from)
                         ->post(route('admins.store'), $this->data);

        $response->assertSessionHasErrors('name')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function email_is_required()
    {
        unset($this->data['email']);

        $response = $this->actingAs($this->admin)
                         ->from($this->from)
                         ->post(route('admins.store'), $this->data);

        $response->assertSessionHasErrors('email')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function email_must_be_email()
    {
        $this->data['email'] = 'not an email';

        $response = $this->actingAs($this->admin)
                         ->from($this->from)
                         ->post(route('admins.store'), $this->data);

        $response->assertSessionHasErrors('email')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function email_must_be_unique_in_users_table()
    {
        $existingUser = User::factory()->create(['email' => 'used@test.com']);
        $this->data['email'] = 'used@test.com';

        $response = $this->actingAs($this->admin)
                         ->from($this->from)
                         ->post(route('admins.store'), $this->data);

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

        $response = $this->actingAs($this->admin)
                         ->from($this->from)
                         ->post(route('admins.store'), $this->data);

        $response->assertSessionHasErrors('email')
                 ->assertRedirect($this->from);
    }
}
