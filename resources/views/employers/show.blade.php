@extends('layouts.dashboard')

@section('title', 'Representante')

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
                :href="route('companies.show', ['company' => $registration->company])"
                label="razão social"
                :description="$registration->company->name"
            />
        </x-slot>

    </x-card.list.description-layout>

    <x-card.list.description-layout title="representante">

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
                label="RG"
                :description="$registration->rg"
            />

            <x-card.list.description-item
                label="registrado"
                :type="$registration->invitation->hasBeenUsed() ? 'title' : 'link'"
                :description="$registration->invitation->hasBeenUsed() ? 'Sim' : 'Não: reenviar e-mail de registro'"
                :href="route('send-invitation', ['invitation' => $registration->invitation])"
            />

        </x-slot>

    </x-card.list.description-layout>

    <div class="flex justify-end mt-4 space-x-2">

        <x-user-management :user="$registration->user"/>

    </div>

@endsection
