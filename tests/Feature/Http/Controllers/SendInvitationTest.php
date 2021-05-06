<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invitation;
use App\Mail\InvitationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendInvitationTest extends TestCase
{
    use RefreshDatabase;

    protected $invitation;

    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();

        $this->invitation = Invitation::factory()->create([
            'email' => 'test@test.com'
        ]);

        $this->coordinator = User::fakeCoordinator();

        Mail::fake();
    }

    /** @test */
    public function coordinator_can_send_an_invitation()
    {
        $invitation = Invitation::factory()->create([
            'email' => 'test@test.com'
        ]);

        $response = $this->actingAs($this->coordinator)
                         ->get(route('send-invitation', [
                             'invitation' => $invitation
                         ]));

        $response->assertRedirect()
                 ->assertSessionHas('status', 'E-mail de registro enviado com sucesso!');
        Mail::assertSent(InvitationEmail::class, function ($mail) use ($invitation) {
            return $mail->hasTo('test@test.com')
                && $mail->invitation->is($invitation);
        });
    }

    /** @test */
    public function cannot_send_an_used_invitation()
    {
        $usedInvitation = Invitation::factory()->used()->create([
            'email' => 'test@test.com',
        ]);

        $response = $this->actingAs($this->coordinator)
                         ->get(route('send-invitation', [
                             'invitation' => $usedInvitation
                         ]));

        $response->assertNotFound();
        Mail::assertNotSent(InvitationEmail::class, function ($mail) use ($usedInvitation) {
            return $mail->hasTo('test@test.com')
                && $mail->invitation->is($usedInvitation);
        });
    }

    /** @test */
    public function guest_cannot_send_an_invitation()
    {
        $response = $this->get(route('send-invitation', [
            'invitation' => $this->invitation
        ]));

        $response->assertRedirect('login');
        Mail::assertNotSent(InvitationEmail::class, function ($mail) {
            return $mail->hasTo('test@test.com')
                && $mail->invitation->is($this->invitation);
        });
    }

    /** @test */
    public function user_without_role_cannot_send_an_invitation()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('send-invitation', [
                             'invitation' => $this->invitation
                         ]));

        $response->assertUnauthorized();
        Mail::assertNotSent(InvitationEmail::class, function ($mail) {
            return $mail->hasTo('test@test.com')
                && $mail->invitation->is($this->invitation);
        });
    }

    /** @test */
    public function instructor_cannot_send_an_invitation()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->get(route('send-invitation', [
                             'invitation' => $this->invitation
                         ]));

        $response->assertUnauthorized();
        Mail::assertNotSent(InvitationEmail::class, function ($mail) {
            return $mail->hasTo('test@test.com')
                && $mail->invitation->is($this->invitation);
        });
    }

    /** @test */
    public function novice_cannot_send_an_invitation()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->get(route('send-invitation', [
                             'invitation' => $this->invitation
                         ]));

        $response->assertUnauthorized();
        Mail::assertNotSent(InvitationEmail::class, function ($mail) {
            return $mail->hasTo('test@test.com')
                && $mail->invitation->is($this->invitation);
        });
    }

    /** @test */
    public function employer_cannot_send_an_invitation()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->get(route('send-invitation', [
                             'invitation' => $this->invitation
                         ]));

        $response->assertUnauthorized();
        Mail::assertNotSent(InvitationEmail::class, function ($mail) {
            return $mail->hasTo('test@test.com')
                && $mail->invitation->is($this->invitation);
        });
    }
}
