@extends('layouts.dashboard')

@section('title', 'Coordenador')

@push('head')
    @livewireStyles
@endpush

@section('content')

    @if (session()->has('status'))
    <x-alert 
        type="success" 
        message="{{ session('status') }}" 
    />
    @endif

    <x-card.list.description-layout title="coordenador">

        <x-slot name="items">

            <x-card.list.description-item
                label="nome"
                :description="$registration->name"
            />

            <livewire:update-registration
                label="e-mail"
                type="text"
                property="email"
                :registration="$registration"
                :updatable="Auth::user()->isCoordinator() || Auth::user()->isAdmin()"
            />

            @if($registration->phones->count() > 0)
            <x-card.list.description-item
                label="telefone"
                :description="$registration->phones->first()->number"
            />
            @endif

            <x-card.list.description-item
                label="registrado"
                :type="$registration->invitation->hasBeenUsed() ? 'title' : 'link'"
                :description="$registration->invitation->hasBeenUsed() ? 'Sim' : 'Não: reenviar e-mail de registro'"
                :href="route('send-invitation', ['invitation' => $registration->invitation])"
            />

        </x-slot>

    </x-card.list.description-layout>

    <div class="flex flex-col space-y-3 justify-end text-center mt-4 lg:flex-row lg:space-y-0 lg:space-x-2">

        <x-user-management :user="$registration->user"/>

        <form
            action="{{ route('admin-coordinators.store') }}"
            method="POST"
        >
            @csrf
            <input type="hidden" name="registration_id" value="{{ $registration->id }}">
            <button
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                tornar administrador
            </button>
        </form>

    </div>
@endsection

@push('footer')
    @livewireScripts
@endpush
