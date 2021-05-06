@extends('layouts.dashboard')

@section('title', 'Administrador')

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

            <x-card.list.description-item
                label="e-mail"
                type="text"
                :description="$registration->email"
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
    <div class="flex justify-end mt-4 space-x-2">
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
