<?php

namespace App\Http\Controllers;

use App\Models\User;

class ComplementaryProfileController extends Controller
{
    public function update(User $user)
    {
        abort_unless(request()->user()->is($user), 401);

        request()->validate([
            'phone'             => ['sometimes', 'required'],
            'address'           => ['sometimes', 'required'],
            'address.street'    => ['sometimes', 'required'],
            'address.number'    => ['sometimes', 'required'],
            'address.district'  => ['sometimes', 'required'],
            'address.city'      => ['sometimes', 'required'],
            'address.state'     => ['sometimes', 'required'],
            'address.country'   => ['sometimes', 'required'],
            'address.cep'       => ['sometimes', 'required', 'size:10'],
        ]);

        request()->whenHas('phone', function ($phone) use ($user) {
            $user->registration->phones[0]->update(['number' => $phone]);
        });

        request()->whenHas('address', function ($address) use ($user) {
            $user->registration->address()->update($address);
        });

        session()->flash('status', 'Informações complementares atualizadas com sucesso!');

        return redirect()->route('profiles.show', ['user' => $user]);
    }
}
