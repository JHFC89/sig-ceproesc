<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersonalProfileController extends Controller
{
    public function update(User $user)
    {
        abort_unless(request()->user()->is($user), 401);

        $data = request()->validate($this->validationRules($user));

        if (request()->has('birthdate')) {
            $data['birthdate'] = $this->formattedBirthdate($data['birthdate']);
        }

        $user->registration()->update($data);

        session()->flash('status', 'Informações pessoais atualizadas com sucesso!');

        return redirect()->route('profiles.show', ['user' => $user]);
    }

    private function validationRules(User $user)
    {
        return [
            'name'              => ['required'],
            'birthdate'         => ['sometimes', 'required'],
            'birthdate.day'     => [
                'sometimes',
                'required',
                'numeric',
                'integer',
            ],
            'birthdate.month'   => [
                'sometimes',
                'required',
                'numeric',
                'integer',
            ],
            'birthdate.year'   => [
                'sometimes',
                'required',
                'numeric',
                'integer',
            ],
            'rg'                => [
                'sometimes',
                'required',
                Rule::unique('App\Models\Registration')->ignore($user->registration->id),
            ],
            'cpf'               => [
                'sometimes',
                'required',
                'size:14',
                Rule::unique('App\Models\Registration')->ignore($user->registration->id),
            ],
            'responsable_name'  => ['sometimes', 'required'],
            'responsable_cpf'   => ['sometimes', 'required', 'size:14'],
        ];
    }

    private function formattedBirthdate($birthdate)
    {
        return Registration::formatBirthdateFromArray($birthdate);
    }
}
