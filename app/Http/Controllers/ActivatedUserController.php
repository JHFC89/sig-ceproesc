<?php

namespace App\Http\Controllers;

use App\Models\User;

class ActivatedUserController extends Controller
{
    public function store()
    {
        abort_unless(request()->user()->isAdmin(), 401);

        $data = request()->validate([
            'user_id' => ['required', 'exists:App\Models\User,id'],
        ]);

        $user = User::find($data['user_id']);

        if ($user->activate()) {
            session()->flash('status', 'Usuário ativado com sucesso!');
        } else {
            session()->flash('status', 'Usuário já está ativo!');
        }

        return redirect()->back();
    }

    public function destroy(User $user)
    {
        abort_unless(request()->user()->isAdmin(), 401);

        if ($user->deactivate()) {
            session()->flash('status', 'Usuário desativado com sucesso!');
        } else {
            session()->flash('status', 'Usuário já está desativado!');
        }

        return redirect()->back();
    }
}
