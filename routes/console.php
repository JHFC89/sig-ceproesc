<?php

use App\Models\{Registration, Role};
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('invite-admin {name} {email}', function ($name, $email) {
        $registration = Registration::create([
            'name'      => $name,
            'role_id'   => Role::whereRole(Role::ADMIN)->id,
        ]);

        $registration->sendInvitationEmail($email);
})->purpose('Invite a new admin to create an account');
