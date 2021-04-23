<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\RandomInvitationCodeGenerator;

class RandomInvitationCodeGeneratorTest extends TestCase
{
    /** @test */
    public function must_be_24_characters_long()
    {
        $generator = new RandomInvitationCodeGenerator;

        $result = $generator->generate();

        $this->assertEquals(24, strlen($result));
    }

    /** @test */
    public function can_only_contain_lowercase_and_uppercase_letters_and_numbers()
    {
        $generator = new RandomInvitationCodeGenerator;

        $result = $generator->generate();

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $result);
    }

    /** @test */
    public function invitation_code_must_be_unique()
    {
        $generator = new RandomInvitationCodeGenerator;

        $invitationCodes = collect(range(1, 100))
            ->map(function ($i) use ($generator) {
                return $generator->generate();
        });

        $this->assertCount(100, $invitationCodes->unique());
    }
}
