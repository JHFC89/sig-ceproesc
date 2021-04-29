<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Mail\InvitationEmail;
use App\Facades\InvitationCode;
use Illuminate\Support\Facades\Mail;
use App\Models\{Invitation, Registration, Role};
use Illuminate\Foundation\Testing\RefreshDatabase;

class InviteAdminViaArtisanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function inviting_an_admin_via_artisan()
    {
        Role::factory()->create(['name' => Role::ADMIN]);
        Role::factory()->create(['name' => Role::COORDINATOR]);
        Mail::fake();
        InvitationCode::shouldReceive('generate')->andReturn('TESTCODE1234');

        $this->artisan('invite-admin', [
            'name'  => 'Fake Admin Name',
            'email' => 'fake-admin@test.com',
        ]);

        $registration = Registration::where('name', 'Fake Admin Name')->first();
        $this->assertEquals('Fake Admin Name', $registration->name);
        $this->assertEquals(Role::ADMIN, $registration->role->name);

        $invitation = Invitation::where('email', 'fake-admin@test.com')->first();
        $this->assertTrue($registration->invitation->is($invitation));
        $this->assertEquals('TESTCODE1234', $invitation->code);

        Mail::assertSent(InvitationEmail::class, function ($mail) use ($invitation) {
            return $mail->hasTo('fake-admin@test.com')
                && $mail->invitation->is($invitation);
        });
    }
}
