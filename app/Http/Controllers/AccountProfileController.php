<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountProfileController extends Controller
{
    public function update(User $user)
    {
        abort_unless(request()->user()->is($user), 401);

        $data = request()->validate([
            'email'     => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => [
                'nullable',
                'min:6',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed',
            ],
            'current_password' => ['present', 'password'],
        ]);

        $user->email = $data['email'];

        $this->updatePassword($user);

        $user->save();

        session()->flash('status', 'Informações da conta atualizadas com sucesso!');

        return redirect()->route('profiles.show', ['user' => $user]);
    }

    private function updatePassword(User $user)
    {
        request()->whenFilled('password', function ($password) use ($user) {
            $user->password = Hash::make($password);
        });
    }
}
