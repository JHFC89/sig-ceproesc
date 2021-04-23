<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Mail\InvitationEmail;
use App\Facades\InvitationCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Role, Company, Invitation, Registration};

class StoreEmployerTest extends TestCase
{
    use RefreshDatabase;

    protected $coordinator;

    protected $role;

    protected $company;

    protected function setUp():void
    {
        parent::setUp();

        $this->coordinator = User::fakeCoordinator();

        $this->role = Role::firstOrCreate(['name' => Role::EMPLOYER]);

        $this->company = Company::factory()->create();
    }

    private function validPostFor(User $user)
    {
        $data = [
            'name'  => 'Test Employer Name',
            'email' => 'employer@test.com',
            'rg'    => '123-123-12',
        ];

        return $this->actingAs($user)
                    ->post(
                        route('companies.employers.store', [
                            'company' => $this->company
                        ]),
                        $data);
    }

    /** @test */
    public function coordinator_can_store_an_employer()
    {
        Mail::fake();

        $invitations = Invitation::count();
        $registrations = Registration::count();

        InvitationCode::shouldReceive('generate')->andReturn('TESTCODE1234');
        $data = [
            'name'  => 'Test Employer Name',
            'email' => 'employer@test.com',
            'rg'    => '123-123-12',
        ];

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.employers.store', [
                             'company' => $this->company
                         ]), $data);

        $response->assertOk()
                 ->assertViewIs('employers.show')
                 ->assertSessionHas('status', 'Representante cadastrado com sucesso!');

        $this->assertEquals($invitations + 1, Invitation::count());
        $this->assertEquals($registrations + 1, Registration::count());
        $invitation = Invitation::where('email', 'employer@test.com')->first();
        $registration = $invitation->registration;
        $this->assertEquals('employer@test.com', $invitation->email);
        $this->assertEquals('TESTCODE1234', $invitation->code);
        $this->assertEquals($this->role->id, $registration->role->id);
        $this->assertEquals($this->role->name, $registration->role->name);
        $this->assertTrue($registration->company->is($this->company));
        $this->assertTrue($registration->invitation->is($invitation));
        $this->assertEquals('Test Employer Name', $registration->name);
        $this->assertEquals('123-123-12', $registration->rg);

        Mail::assertSent(InvitationEmail::class, function ($mail) use ($invitation) {
            return $mail->hasTo('employer@test.com')
                && $mail->invitation->is($invitation);
        });
    }

    /** @test */
    public function guest_cannot_store_an_employer()
    {
        $invitations = Invitation::count();
        $registrations = Registration::count();
        $company = Company::factory()->create();
        $data = [
            'name'  => 'Test Employer Name',
            'email' => 'employer@test.com',
            'rg'    => '123-123-12',
        ];

        $response = $this->post(
            route('companies.employers.store', ['company' => $company]),
            $data
        );

        $response->assertRedirect('login');
        $this->assertEquals($invitations, Invitation::count());
        $this->assertEquals($registrations, Registration::count());
    }

    /** @test */
    public function user_without_role_cannot_store_an_employer()
    {
        $user = User::factory()->create();
        $invitations = Invitation::count();
        $registrations = Registration::count();

        $response = $this->validPostFor($user);

        $response->assertUnauthorized();
        $this->assertEquals($invitations, Invitation::count());
        $this->assertEquals($registrations, Registration::count());
    }

    /** @test */
    public function instructor_cannot_store_an_employer()
    {
        $instructor = User::fakeInstructor();
        $invitations = Invitation::count();
        $registrations = Registration::count();

        $response = $this->validPostFor($instructor);


        $response->assertUnauthorized();
        $this->assertEquals($invitations, Invitation::count());
        $this->assertEquals($registrations, Registration::count());
    }

    /** @test */
    public function novice_cannot_store_an_employer()
    {
        $novice = User::fakeNovice();
        $invitations = Invitation::count();
        $registrations = Registration::count();

        $response = $this->validPostFor($novice);


        $response->assertUnauthorized();
        $this->assertEquals($invitations, Invitation::count());
        $this->assertEquals($registrations, Registration::count());
    }

    /** @test */
    public function employer_cannot_store_an_employer()
    {
        $employer = User::fakeEmployer();
        $invitations = Invitation::count();
        $registrations = Registration::count();

        $response = $this->validPostFor($employer);


        $response->assertUnauthorized();
        $this->assertEquals($invitations, Invitation::count());
        $this->assertEquals($registrations, Registration::count());
    }

    /** @test */
    public function name_is_required()
    {
        $data = [
            'email' => 'employer@test.com',
            'rg'    => '123-123-12',
        ];
        $from = route('companies.employers.create', [
            'company' => $this->company
        ]);

        $response = $this->actingAs($this->coordinator)
                         ->from($from)
                         ->post(route('companies.employers.store', [
                             'company' => $this->company
                         ]), $data);

        $response->assertSessionHasErrors('name')
                 ->assertRedirect($from);
    }

    /** @test */
    public function email_is_required()
    {
        $data = [
            'name'  => 'Test Employer Name',
            'rg'    => '123-123-12',
        ];
        $from = route('companies.employers.create', [
            'company' => $this->company
        ]);

        $response = $this->actingAs($this->coordinator)
                         ->from($from)
                         ->post(route('companies.employers.store', [
                             'company' => $this->company
                         ]), $data);

        $response->assertSessionHasErrors('email')
                 ->assertRedirect($from);
    }

    /** @test */
    public function email_must_be_email()
    {
        $data = [
            'name'  => 'Test Employer Name',
            'email' => 'not an email',
            'rg'    => '123-123-12',
        ];
        $from = route('companies.employers.create', [
            'company' => $this->company
        ]);

        $response = $this->actingAs($this->coordinator)
                         ->from($from)
                         ->post(route('companies.employers.store', [
                             'company' => $this->company
                         ]), $data);

        $response->assertSessionHasErrors('email')
                 ->assertRedirect($from);
    }

    /** @test */
    public function email_must_be_unique()
    {
        $existingUser = User::factory()->create(['email' => 'used@test.com']);
        $data = [
            'name'  => 'Test Employer Name',
            'email' => 'used@test.com',
            'rg'    => '123-123-12',
        ];
        $from = route('companies.employers.create', [
            'company' => $this->company
        ]);

        $response = $this->actingAs($this->coordinator)
                         ->from($from)
                         ->post(route('companies.employers.store', [
                             'company' => $this->company
                         ]), $data);

        $response->assertSessionHasErrors('email')
                 ->assertRedirect($from);
    }

    /** @test */
    public function rg_is_required()
    {
        $data = [
            'name'  => 'Test Employer Name',
            'email' => 'employer@test.com',
        ];
        $from = route('companies.employers.create', [
            'company' => $this->company
        ]);

        $response = $this->actingAs($this->coordinator)
                         ->from($from)
                         ->post(route('companies.employers.store', [
                             'company' => $this->company
                         ]), $data);

        $response->assertSessionHasErrors('rg')
                 ->assertRedirect($from);
    }

    /** @test */
    public function rg_must_be_unique()
    {
        $existingUser = User::factory()->create();
        $existingUser->registration->update(['rg' => '123-123-12']);
        $data = [
            'name'  => 'Test Employer Name',
            'email' => 'employer@test.com',
            'rg'    => '123-123-12',
        ];
        $from = route('companies.employers.create', [
            'company' => $this->company
        ]);

        $response = $this->actingAs($this->coordinator)
                         ->from($from)
                         ->post(route('companies.employers.store', [
                             'company' => $this->company
                         ]), $data);

        $response->assertSessionHasErrors('rg')
                 ->assertRedirect($from);
    }
}
