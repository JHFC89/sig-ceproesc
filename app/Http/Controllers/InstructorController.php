<?php

namespace App\Http\Controllers;

use App\Models\{Registration, Role};

class InstructorController extends Controller
{
    public function index()
    {
        abort_if(request()->user()->cannot('viewAny', Registration::class), 401);

        $registrations = Registration::whereInstructor()->get();

        return view('instructors.index', compact('registrations'));
    }

    public function show(Registration $registration)
    {
        abort_if(request()->user()->cannot('view', $registration), 401);

        abort_unless($registration->isForInstructor(), 404);

        return view('instructors.show', compact('registration'));
    }

    public function create()
    {
        abort_if(request()->user()->cannot('create', Registration::class), 401);

        return view('instructors.create');
    }

    public function store()
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
            'name'      => $data['name'],
            'birthdate' => Registration::formatBirthdateFromArray($data['birthdate']),
            'role_id'   => Role::whereRole(Role::INSTRUCTOR)->id,
            'rg'        => $data['rg'],
            'cpf'       => $data['cpf'],
            'ctps'      => $data['ctps'],
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

        session()->flash('status', 'Instrutor cadastrado com sucesso!');

        return view('instructors.show', compact('registration'));
    }
}
