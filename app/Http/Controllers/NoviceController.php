<?php

namespace App\Http\Controllers;

use App\Models\{Registration, Role, Company};

class NoviceController extends Controller
{
    public function index(Company $company)
    {
        abort_if(request()->user()->cannot('viewAny', [
            Registration::class,
            $company,
        ]), 401);

        $registrations = Registration::whereEmployer($company->id)->get();

        return view('novices.index', compact('company', 'registrations'));
    }

    public function show(Registration $registration)
    {
        abort_if(request()->user()->cannot('view', $registration), 401);

        abort_unless($registration->isForNovice(), 404);

        return view('novices.show', compact('registration'));
    }

    public function create(Company $company)
    {
        abort_if(request()->user()->cannot('create', Registration::class), 401);

        return view('novices.create', compact('company'));
    }

    public function store(Company $company)
    {
        abort_if(request()->user()->cannot('create', Registration::class), 401);

        $data = request()->validate([
            'name'              => ['required'],
            'email'             => [
                'required',
                'email',
                'unique:users',
                'unique:invitations',
            ],
            'birthdate'         => ['required'],
            'birthdate.day'     => ['required', 'numeric', 'integer'],
            'birthdate.month'   => ['required', 'numeric', 'integer'],
            'birthdate.year'    => ['required', 'numeric', 'integer'],
            'rg'                => ['required', 'unique:registrations'],
            'cpf'               => [
                'required',
                'size:14',
                'unique:registrations',
            ],
            'ctps'              => ['required', 'unique:registrations'],
            'responsable_name'  => ['required'],
            'responsable_cpf'   => ['required', 'size:14'],
            'phone'             => ['required'],
            'address'           => ['required'],
            'address.street'    => ['required'],
            'address.number'    => ['required'],
            'address.district'  => ['required'],
            'address.city'      => ['required'],
            'address.state'     => ['required'],
            'address.country'   => ['required'],
            'address.cep'       => ['required', 'size:10'],
        ]);
        
        $registration = Registration::create([
            'name'              => $data['name'],
            'birthdate'         => Registration::formatBirthdateFromArray(request()->birthdate),
            'rg'                => $data['rg'],
            'cpf'               => $data['cpf'],
            'ctps'              => $data['ctps'],
            'responsable_name'  => $data['responsable_name'],
            'responsable_cpf'   => $data['responsable_cpf'],
            'role_id'           => Role::whereRole(Role::NOVICE)->id,
            'employer_id'       => $company->id,
        ]);

        $registration->phones()->create(['number' => $data['phone']]);

        $registration->address()->create([
            'street'    => $data['address']['street'],
            'number'    => $data['address']['number'],
            'district'  => $data['address']['district'],
            'city'      => $data['address']['city'],
            'state'     => $data['address']['state'],
            'country'   => $data['address']['country'],
            'cep'       => $data['address']['cep'],
        ]);

        $registration->sendInvitationEmail($data['email']);

        session()->flash('status', 'Aprendiz cadastrado com sucesso!');

        return view('novices.show', compact('registration'));
    }
}
