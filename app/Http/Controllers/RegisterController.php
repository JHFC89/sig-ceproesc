<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function store()
    {
        $invitation = Invitation::findByCode(request()->confirmation_code);

        abort_if($invitation->hasBeenUsed(), 404);

        $validatedData = request()->validate([
            'email' => [
                'required',
                'email',
                "in:{$invitation->email}",
                'unique:users',
            ],
            'password' => [
                'required',
                'min:6',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed'
            ],
        ]);

        $user = $invitation->createUserFromArray($validatedData);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
