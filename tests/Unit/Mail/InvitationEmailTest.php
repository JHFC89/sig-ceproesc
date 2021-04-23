<?php

namespace Tests\Unit\Mail;

use App\Models\Invitation;
use App\Mail\InvitationEmail;
use Tests\TestCase;

class InvitationEmailTest extends TestCase
{
    /** @test */
    public function email_contains_a_link_to_accept_the_invitation()
    {
        $invitation = Invitation::factory()->make([
            'email' => 'test@test.com',
            'code'  => 'TESTcode1234',
        ]);

        $email = new InvitationEmail($invitation);

        $email->assertSeeInHtml(route('invitations.show', [
            'code' => 'TESTcode1234'
        ]));
    }

    /** @test */
    public function email_has_the_correct_subject()
    {
        $invitation = Invitation::factory()->make();

        $email = new InvitationEmail($invitation);

        $this->assertEquals(
            'Seu acesso ao sistema SIG-CEPROESC',
            $email->build()->subject
        );
    }
}
