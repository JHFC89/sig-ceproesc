<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Registration;

class AdminController extends Controller
{
    public function index()
    {
        abort_unless(request()->user()->isAdmin(), 401);

        $registrations = Registration::whereAdmin()->get();

        return view('admins.index', compact('registrations'));
    }

    public function show(Registration $registration)
    {
        abort_unless(request()->user()->isAdmin(), 401);

        abort_unless($registration->isForAdmin(), 404);

        return view('admins.show', compact('registration'));
    }

    public function create()
    {
        abort_unless(request()->user()->isAdmin(), 401);

        return view('admins.create');
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
        ]);

        $registration = Registration::create([
            'name'      => $data['name'],
            'role_id'   => Role::whereRole(Role::ADMIN)->id,
        ]);

        $registration->sendInvitationEmail($data['email']);

        session()->flash('status', 'Administrador cadastrado com sucesso!');

        return view('admins.show', compact('registration'));
    }
}
