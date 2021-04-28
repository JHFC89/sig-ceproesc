<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Mail\InvitationEmail;
use App\Facades\InvitationCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Role, Invitation, Registration};

class StoreCoordinatorTest extends TestCase
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
            'name'      => 'Test Coordinator Name',
            'email'     => 'coordinator@test.com',
            'phone'     => '1234567',
        ];

        $this->from = route('coordinators.create');

        Role::firstOrCreate(['name' => Role::INSTRUCTOR]);
    }

    /** @test */
    public function admin_can_store_an_coordinator()
    {
        Mail::fake();
        InvitationCode::shouldReceive('generate')->andReturn('TESTCODE1234');

        $data = $this->data;

        $response = $this->actingAs($this->admin)
                         ->post(route('coordinators.store'), $data);

        $response->assertOk()
                 ->assertViewHas('registration')
                 ->assertViewIs('coordinators.show')
                 ->assertSessionHas('status', 'Coordenador cadastrado com sucesso!');

        $registration = Registration::where('name', 'Test Coordinator Name')->first();
        $this->assertEquals($data['name'], $registration->name);
        $this->assertEquals($data['phone'], $registration->phones[0]->number);
        $this->assertEquals(Role::COORDINATOR, $registration->role->name);

        $invitation = Invitation::where('email', 'coordinator@test.com')->first();
        $this->assertTrue($registration->invitation->is($invitation));
        $this->assertEquals('TESTCODE1234', $invitation->code);

        Mail::assertSent(InvitationEmail::class, function ($mail) use ($invitation) {
            return $mail->hasTo('coordinator@test.com')
                && $mail->invitation->is($invitation);
        });
    }

    /** @test */
    public function guest_cannot_store_an_coordinator()
    {
        $response = $this->post(route('coordinators.store'), $this->data);

        $response->assertRedirect('login');
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function user_without_role_cannot_store_an_coordinator()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->post(route('coordinators.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function coordinator_cannot_store_an_coordinator()
    {
        $coordinator = User::fakeCoordinator();

        $response = $this->actingAs($coordinator)
                         ->post(route('coordinators.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function instructor_cannot_store_an_coordinator()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->post(route('coordinators.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function novice_cannot_store_an_coordinator()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->post(route('coordinators.store'), $this->data);

        $response->assertUnauthorized();
        $this->assertNull(Registration::where('name', $this->data['name'])->first());
        $this->assertNull(Invitation::where('email', $this->data['email'])->first());
    }

    /** @test */
    public function employer_cannot_store_an_coordinator()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->post(route('coordinators.store'), $this->data);

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
                         ->post(route('coordinators.store'), $this->data);

        $response->assertSessionHasErrors('name')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function email_is_required()
    {
        unset($this->data['email']);

        $response = $this->actingAs($this->admin)
                         ->from($this->from)
                         ->post(route('coordinators.store'), $this->data);

        $response->assertSessionHasErrors('email')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function email_must_be_email()
    {
        $this->data['email'] = 'not an email';

        $response = $this->actingAs($this->admin)
                         ->from($this->from)
                         ->post(route('coordinators.store'), $this->data);

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
                         ->post(route('coordinators.store'), $this->data);

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
                         ->post(route('coordinators.store'), $this->data);

        $response->assertSessionHasErrors('email')
                 ->assertRedirect($this->from);
    }

    /** @test */
    public function phone_is_required()
    {
        unset($this->data['phone']);

        $response = $this->actingAs($this->admin)
                         ->from($this->from)
                         ->post(route('coordinators.store'), $this->data);

        $response->assertSessionHasErrors('phone')
                 ->assertRedirect($this->from);
    }
}
