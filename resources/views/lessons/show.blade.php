@extends('layouts.dashboard')

@section('title', 'Registro De Aula')

@section('content')

    <x-card.list.description-layout title="detalhes da aula">
        <x-slot name="items">
            @if(Auth::user()->isNovice())
            <x-card.list.description-item label="aprendiz" :description="$lesson->novices->find(Auth::user())->name"/>
            @endif
            <x-card.list.description-item label="instrutor" :description="$lesson->instructor->name"/>
            <x-card.list.description-item label="data" :description="$lesson->formatted_date"/>
            @if(Auth::user()->isNovice())
                <x-card.list.description-item label="turma" :description="Auth::user()->class"/>
            @else
            <x-card.list.description-item label="turma" :description="$lesson->formatted_course_classes"/>
            @endif
            <x-card.list.description-item label="disciplina" :description="$lesson->discipline"/>
            <x-card.list.description-item label="carga horária" :description="$lesson->hourly_load"/>
            @if(Auth::user()->isNovice() && $lesson->isRegistered())
            <x-card.list.description-item label="presença" :description="Auth::user()->presentForLesson($lesson) ? 'presente' : 'ausente'"/>
            <x-card.list.description-item type="text" label="observação" :description="Auth::user()->observationForLesson($lesson) ?: 'Nenhuma obervação registrada'"/>
            @endif
            @if($lesson->isRegistered())
            <x-card.list.description-item type="text" label="registro" :description="$lesson->register"/>
            @endif
        </x-slot>
    </x-card.list.description-layout>

    @if(Auth::user()->isInstructor() || Auth::user()->isEmployer())
    <x-card.list.table-layout title="lista de presença">

        <x-slot name="header">
            <x-card.list.table-header class="col-span-1" name="código"/>
            <x-card.list.table-header class="{{ $lesson->isRegistered() ? 'col-span-3' : 'col-span-6'}}" name="nome"/>
            <x-card.list.table-header class="col-span-2" name="turma"/>
            @if($lesson->isRegistered())
            <x-card.list.table-header class="text-center col-span-2" name="presença"/>
            <x-card.list.table-header class="col-span-4" name="observação"/>
            @endif
        </x-slot>

        <x-slot name="body">

            @foreach($lesson->novices as $novice)
                @if(Auth::user()->isEmployerOf($novice) || Auth::user()->isInstructor())
                <x-card.list.table-row>
                    <x-slot name="items">

                        <x-card.list.table-body-item class="col-span-1">
                            <x-slot name="item">
                                <span>{{ $novice->code }}</span>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="{{ $lesson->isRegistered() ? 'col-span-3' : 'col-span-6'}}">
                            <x-slot name="item">
                                <span>{{ $novice->name }}</span>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="col-span-2">
                            <x-slot name="item">
                                <span>{{ $novice->class }}</span>
                            </x-slot>
                        </x-card.list.table-body-item>

                        @if($lesson->isRegistered())
                        <x-card.list.table-body-item class="col-span-2">
                            <x-slot name="item">
                                <div class="flex items-center justify-center h-full">
                                    <x-icons.active class="w-2 h-2" :active="$novice->presentForLesson($lesson)"/>
                                </div>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="col-span-4">
                            <x-slot name="item">
                                <span class="normal-case">{!! $novice->observationForLesson($lesson) ?: '<span class="text-gray-300">Nenhuma observação registrada</span>' !!}</span>
                            </x-slot>
                        </x-card.list.table-body-item>
                        @endif

                    </x-slot>
                </x-card.list.table-row>
                @endif
            @endforeach

        </x-slot>

    </x-card.list.table-layout>
    @endif

@endsection
