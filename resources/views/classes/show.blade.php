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
                label="cidade"
                :description="$courseClass->city"
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
            <x-card.list.description-item
                label="carga horária prática total"
                :description="($courseClass->totalPracticalDaysDuration() / 60) . ' hr'"
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
                class="mx-auto max-w-screen-xl grid gap-6 lg:grid-cols-2 xl:grid-cols-3"
                :group="$courseClass"
            />
        </div>

    </div>
    <div class="flex justify-end mt-4 space-x-4">
        @if ($courseClass->hasLessons())
            @if (Auth::user()->isCoordinator())
            <a
                href="{{ route('classes.subscriptions.create', ['courseClass' => $courseClass]) }}"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                cadastrar aprendizes
            </a>
            @endif
            <a
                href="{{ route('classes.lessons.index', ['courseClass' => $courseClass]) }}"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                ver aulas cadastradas
            </a>
        @else
            @if (Auth::user()->isCoordinator())
            <a
                href="{{ route('classes.lessons.create', ['courseClass' => $courseClass]) }}"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                cadastrar aulas
            </a>
            @endif
        @endif
        </div>
@endsection
