<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Registration;

class CoordinatorController extends Controller
{
    public function index()
    {
        abort_unless(request()->user()->isAdmin(), 401);

        $registrations = Registration::whereCoordinator()->get();

        return view('coordinators.index', compact('registrations'));
    }

    public function show(Registration $registration)
    {
        abort_unless(request()->user()->isAdmin(), 401);

        abort_unless($registration->isForCoordinator(), 404);

        return view('coordinators.show', compact('registration'));
    }

    public function create()
    {
        abort_unless(request()->user()->isAdmin(), 401);

        return view('coordinators.create');
    }

    public function store()
    {
        abort_unless(request()->user()->isAdmin(), 401);

        $data = request()->validate([
            'name'              => ['required'],
            'email'             => [
                'required',
                'email',
                'unique:users',
                'unique:invitations',
            ],
            'phone'             => ['required'],
        ]);

        $registration = Registration::create([
            'name'      => $data['name'],
            'role_id'   => Role::whereRole(Role::COORDINATOR)->id,
        ]);

        $registration->phones()->create(['number' => $data['phone']]);

        $registration->sendInvitationEmail($data['email']);

        session()->flash('status', 'Coordenador cadastrado com sucesso!');

        return view('coordinators.show', compact('registration'));
    }
}
