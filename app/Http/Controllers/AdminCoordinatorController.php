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

        $this->checkRoleConditions($registration, $coordinator);

        Role::promoteToAdmin($registration, $coordinator);

        $registration->refresh();

        session()->flash('status', 'Administrador cadastrado com sucesso!');

        return view('admins.show', compact('registration'));
    }

    private function checkRoleConditions($registration, $coordinator)
    {
        if (empty($coordinator)) {
            $abort = ! $registration->isForCoordinator();
        } else {
            $isCoordinator = $coordinator->isCoordinator();

            $isAdmin = $coordinator->isAdmin();

            $abort = ! $isCoordinator || $isCoordinator && $isAdmin;
        }

        abort_if($abort, 404);
    }
}
