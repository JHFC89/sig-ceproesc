<?php

namespace App\Http\Controllers;

use App\Models\User;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        abort_unless(request()->user()->is($user), 401);

        $user->load('registration.phones', 'registration.address');

        return view('profiles.show', compact('user'));
    }
}
