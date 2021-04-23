<?php

namespace App;

class RandomInvitationCodeGenerator implements InvitationCodeGenerator
{
    public function generate()
    {
        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        return substr(str_shuffle(str_repeat($pool, 24)), 0, 24);
    }
}
