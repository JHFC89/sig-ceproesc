<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Role;
use Illuminate\Http\Request;

class AdminCoordinatorController extends Controller
{
    public function store()
    {
        abort_unless(request()->user()->isAdmin(), 401);

        $data = request()->validate([
            'registration_id' => [
                'required',
                'exists:App\Models\Registration,id'
            ],
        ]);

        $registration = Registration::find($data['registration_id']);

        $coordinator = $registration->user;

        abort_if($this->invalidPromotion($registration, $coordinator), 404);

        Role::promoteToAdmin($registration, $coordinator);

        $registration->refresh();

        session()->flash('status', 'Administrador cadastrado com sucesso!');

        return view('admins.show', compact('registration'));
    }

    public function delete(Registration $registration)
    {
        abort_unless(request()->user()->isAdmin(), 401);

        $adminCoordinator = $registration->user;

        abort_if($this->invalidDemotion($registration, $adminCoordinator), 404);

        Role::demoteToCoordinator($registration, $adminCoordinator);

        session()->flash('status', 'Coordenador não é mais Administrador.');

        return view('coordinators.show', compact('registration'));
    }

    private function invalidPromotion($registration, $coordinator)
    {
        if (empty($coordinator)) {
            $invalid = ! $registration->isForCoordinator();
        } else {
            $isCoordinator = $coordinator->isCoordinator();

            $isAdmin = $coordinator->isAdmin();

            $invalid = ! $isCoordinator || $isCoordinator && $isAdmin;
        }

        return $invalid;
    }

    private function invalidDemotion($registration, $user)
    {
        if (empty($user)) {
            $invalid = ! $registration->isForAdmin();
        } else {
            $invalid = ! ($user->isCoordinator() && $user->isAdmin());
        }

        return $invalid;
    }
}
