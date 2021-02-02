@extends('layouts.dashboard')

@section('title', 'Registro De Aula')

@section('content')

    @if (session()->has('status'))
    <x-alert 
        type="success" 
        message="{{ session('status') }}" 
    />
    @endif

    <x-card.list.description-layout title="disciplina">
        <x-slot name="items">
            <x-card.list.description-item
                label="nome"
                :description="$discipline->name"
            />
            <x-card.list.description-item
                label="módulo"
                :description="$discipline->basic ? 'básico' : 'específico'"
            />
            <x-card.list.description-item
                label="carga horária"
                :description="$discipline->duration . ' hr'"
            />
            <x-card.list.description-item
                label="instrutores"
                :description="$discipline->formatted_instructors"
            />
        </x-slot>
    </x-card.list.description-layout>

@endsection
