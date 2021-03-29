@extends('layouts.dashboard')

@section('title', 'Cadastrar Aulas Para Turma')

@push('head')
    @livewireStyles
@endpush

@section('content')

    <x-card.list.description-layout title="turma">
        <x-slot name="items">
            <x-card.list.description-item
                label="nome"
                :description="$courseClass->name"
            />
            <x-card.list.description-item
                label="carga horária módulo básico"
                :description="$courseClass->course->basicDisciplinesDuration() . ' hr'"
            />
            <x-card.list.description-item
                label="carga horária módulo específico"
                :description="$courseClass->course->specificDisciplinesDuration() . ' hr'"
            />
        </x-slot>
    </x-card.list.description-layout>

    <livewire:create-lesson-discipline-duration-counter :courseClass="$courseClass"/>

    <livewire:create-lesson-form :courseClass="$courseClass"/>

@endsection

@push('footer')
    @livewireScripts
@endpush
