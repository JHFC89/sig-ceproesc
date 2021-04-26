<?php

namespace App\Http\Controllers;

use App\Models\{Role, Invitation, Registration, Company};

class EmployerController extends Controller
{
    public function index(Company $company)
    {
        abort_if(request()->user()->cannot('viewAny', Company::class), 401);

        $registrations = Registration::employersForCompany($company);

        return view('employers.index', compact('company', 'registrations'));
    }

    public function show(Registration $registration)
    {
        abort_if(request()->user()->cannot('viewAny', Company::class), 401);

        abort_unless($registration->isForEmployer(), 404);

        return view('employers.show', compact('registration'));
    }

    public function create(Company $company)
    {
        abort_if(request()->user()->cannot('create', Company::class), 401);

        return view('employers.create', compact('company'));
    }

    public function store(Company $company)
    {
        abort_if(request()->user()->cannot('create', Company::class), 401);

        $data = request()->validate([
            'name'  => ['required'],
            'email' => [
                'required',
                'email',
                'unique:users',
                'unique:invitations'
            ],
            'rg'    => ['required', 'unique:registrations'],
        ]);

        $registration = Registration::create([
            'name'          => $data['name'],
            'rg'            => $data['rg'],
            'role_id'       => Role::whereRole(Role::EMPLOYER)->id,
            'company_id'    => $company->id,
        ]);

        $registration->sendInvitationEmail($data['email']);

        session()->flash('status', 'Representante cadastrado com sucesso!');

        return view('employers.show', compact('registration'));
    }
}
