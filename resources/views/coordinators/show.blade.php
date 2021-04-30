@extends('layouts.dashboard')

@section('title', 'Coordenador')

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

            <x-card.list.description-item
                label="e-mail"
                type="text"
                :description="$registration->email"
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
                :description="$registration->invitation->hasBeenUsed() ? 'sim' : 'não: reenviar link de registro'"
                :link="'#'"
            />

        </x-slot>

    </x-card.list.description-layout>

    <div class="flex justify-end mt-4 space-x-2">
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
