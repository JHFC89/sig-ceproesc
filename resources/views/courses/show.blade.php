@extends('layouts.dashboard')

@section('title', 'Programa')

@section('content')

    @if (session()->has('status'))
    <x-alert 
        type="success" 
        message="{{ session('status') }}" 
    />
    @endif

    <x-card.list.description-layout title="programa">
        <x-slot name="items">
            <x-card.list.description-item
                label="nome"
                :description="$course->name"
            />
            <x-card.list.description-item
                label="carga horária total"
                :description="$course->duration . ' hr'"
            />
            <x-card.list.description-item
                label="carga horária módulo básico"
                :description="$course->basicDisciplinesDuration() . ' hr'"
            />
            <x-card.list.description-item
                label="carga horária módulo específico"
                :description="$course->specificDisciplinesDuration() . ' hr'"
            />
        </x-slot>
    </x-card.list.description-layout>

    <x-card.list.table-layout title="disciplinas módulo básico">

        <x-slot name="header">

            <x-card.list.table-header class="col-span-4" name="nome"/>
            <x-card.list.table-header class="col-span-4 text-center" name="carga horária"/>

        </x-slot>

        <x-slot name="body">

            @foreach($course->basicDisciplines() as $discipline)

            <x-card.list.table-row>
                <x-slot name="items">

                    <x-card.list.table-body-item class="col-span-4">
                        <x-slot name="item">
                            <span class="flex items-center h-full">{{ $discipline->name }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <div class="flex items-center justify-center h-full w-full">
                                <span>{{ $discipline->duration }} hrs</span>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                </x-slot>

            </x-card.list.table-row>

            @endforeach

        </x-slot>

    </x-card.list.table-layout>

    <x-card.list.table-layout title="disciplinas módulo específico">

        <x-slot name="header">

            <x-card.list.table-header class="col-span-4" name="nome"/>
            <x-card.list.table-header class="col-span-4 text-center" name="carga horária"/>

        </x-slot>

        <x-slot name="body">

            @foreach($course->specificDisciplines() as $discipline)

            <x-card.list.table-row>
                <x-slot name="items">

                    <x-card.list.table-body-item class="col-span-4">
                        <x-slot name="item">
                            <span class="flex items-center h-full">{{ $discipline->name }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <div class="flex items-center justify-center h-full w-full">
                                <span>{{ $discipline->duration }} hrs</span>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                </x-slot>

            </x-card.list.table-row>

            @endforeach

        </x-slot>

    </x-card.list.table-layout>

@endsection
