@extends('layouts.dashboard')

@section('title', 'Aprendiz')

@section('content')

    @if (session()->has('status'))
    <x-alert 
        type="success" 
        message="{{ session('status') }}" 
    />
    @endif

    <x-card.list.description-layout title="empresa">

        <x-slot name="items">
            <x-card.list.description-item
                type="link"
                :href="route('companies.show', ['company' => $registration->employer])"
                label="razão social"
                :description="$registration->employer->name"
            />
        </x-slot>

    </x-card.list.description-layout>

    <x-card.list.description-layout title="aprendiz">

        <x-slot name="items">

            <x-card.list.description-item
                label="nome"
                :description="$registration->name"
            />

            <x-card.list.description-item
                label="e-mail"
                type="text"
                :description="$registration->email"
            />

            <x-card.list.description-item
                label="data de nascimento"
                :description="$registration->formatted_birthdate"
            />

            <x-card.list.description-item
                label="RG"
                :description="$registration->rg"
            />

            <x-card.list.description-item
                label="CPF"
                :description="$registration->cpf"
            />

            <x-card.list.description-item
                label="CTPS"
                :description="$registration->ctps"
            />

            <x-card.list.description-item
                label="nome do responsável"
                :description="$registration->responsable_name"
            />

            <x-card.list.description-item
                label="CPF do responsável"
                :description="$registration->responsable_cpf"
            />

            <x-card.list.description-item
                label="telefone"
                :description="$registration->phones->first()->number"
            />

            @if (Auth::user()->isCoordinator() || Auth::user()->isAdmin())
            <x-card.list.description-item
                label="registrado"
                :type="$registration->invitation->hasBeenUsed() ? 'title' : 'link'"
                :description="$registration->invitation->hasBeenUsed() ? 'Sim' : 'Não: reenviar e-mail de registro'"
                :href="route('send-invitation', ['invitation' => $registration->invitation])"
            />
            @endif

        </x-slot>

    </x-card.list.description-layout>

    <x-card.list.description-layout title="endereço">

        <x-slot name="items">

            <x-card.list.description-item
                label="rua"
                :description="$registration->address->street . ', ' . $registration->address->number"
            />

            <x-card.list.description-item
                label="bairro"
                :description="$registration->address->district"
            />

            <x-card.list.description-item
                label="cidade"
                :description="$registration->address->city . '/' . $registration->address->state"
            />

            <x-card.list.description-item
                label="CEP"
                :description="$registration->address->cep"
            />

            <x-card.list.description-item
                label="país"
                :description="$registration->address->country"
            />

        </x-slot>

    </x-card.list.description-layout>

    <div class="flex flex-col space-y-3 justify-end text-center mt-4 lg:flex-row lg:space-y-0 lg:space-x-2">

        @if ((Auth::user()->isCoordinator() || Auth::user()->isEmployer()) && $registration->user !== null)
            @if ($registration->user->courseClass !== null)
            <a
                href="{{ route('classes.lessons.index', ['courseClass' => $registration->user->courseClass]) }}"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                ver aulas
            </a>
            <a
                href="{{ route('novices.frequencies.show', ['registration' => $registration]) }}"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                ver frequência
            </a>
            @endif
        @endif

        <x-user-management :user="$registration->user"/>

    </div>

@endsection
