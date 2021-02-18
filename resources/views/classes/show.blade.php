@extends('layouts.dashboard')

@section('title', 'Turma')

@section('content')

    @if (session()->has('status'))
    <x-alert 
        type="success" 
        message="{{ session('status') }}" 
    />
    @endif

    <x-card.list.description-layout title="turma">
        <x-slot name="items">
            <x-card.list.description-item
                label="nome"
                :description="$courseClass->name"
            />
            <x-card.list.description-item
                label="carga horária teórica total"
                :description="$courseClass->course->duration . ' hr'"
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

    <div class="flex w-full space-x-4">

        <x-card.list.table-layout class="w-1/2" title="disciplinas módulo básico">

            <x-slot name="header">

                <x-card.list.table-header class="col-span-8" name="nome"/>
                <x-card.list.table-header class="col-span-4 text-center" name="carga horária"/>

            </x-slot>

            <x-slot name="body">

                @foreach($courseClass->course->basicDisciplines() as $discipline)

                <x-card.list.table-row>
                    <x-slot name="items">

                        <x-card.list.table-body-item class="col-span-8">
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

        <x-card.list.table-layout class="w-1/2" title="disciplinas módulo específico">

            <x-slot name="header">

                <x-card.list.table-header class="col-span-8" name="nome"/>
                <x-card.list.table-header class="col-span-4 text-center" name="carga horária"/>

            </x-slot>

            <x-slot name="body">

                @foreach($courseClass->course->specificDisciplines() as $discipline)

                <x-card.list.table-row>
                    <x-slot name="items">

                        <x-card.list.table-body-item class="col-span-8">
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

    </div>

    <div>
        <h2 class="text-xl font-medium text-gray-700 capitalize">cronograma</h2>

        <div class="px-6 py-6 mt-4 capitalize bg-white shadow divide-y rounded-md">
            <x-course-class.schedule 
                class="grid grid-cols-3 gap-6"
                :group="$courseClass"
            />
        </div>

    </div>
@endsection
