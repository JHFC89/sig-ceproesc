@extends('layouts.dashboard')

@section('title', 'Administrador')

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

    <x-card.list.description-layout title="administrador">

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

            <x-card.list.description-item
                label="registrado"
                :type="$registration->invitation->hasBeenUsed() ? 'title' : 'link'"
                :description="$registration->invitation->hasBeenUsed() ? 'Sim' : 'Não: reenviar e-mail de registro'"
                :href="route('send-invitation', ['invitation' => $registration->invitation])"
            />

        </x-slot>

    </x-card.list.description-layout>

    @if ($registration->canBeDemotedToCoordinator())
    <div class="flex flex-col space-y-3 justify-end text-center mt-4 lg:flex-row lg:space-y-0 lg:space-x-2">
        <form
            action="{{ route('admin-coordinators.destroy', ['registration' => $registration->id]) }}"
            method="POST"
        >
            @method('DELETE')
            @csrf
            <button
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-red-600 hover:bg-red-500 hover:text-red-100 rounded-md shadown"
            >
                remover acesso administrativo
            </button>
        </form>
    </div>
    @endif

@endsection

@push('footer')
    @livewireScripts
@endpush
