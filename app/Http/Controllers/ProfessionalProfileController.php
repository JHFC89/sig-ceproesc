<?php

namespace App\Http\Controllers;

use App\Models\User;

class ProfessionalProfileController extends Controller
{
    public function update(User $user)
    {
        abort_unless(request()->user()->is($user), 401);

        $data = request()->validate([
            'ctps' => ['required'],
        ]);

        $user->registration()->update($data);

        session()->flash('status', 'Informações profissionais atualizadas com sucesso!');

        return redirect()->route('profiles.show', ['user' => $user]);
    }
}
