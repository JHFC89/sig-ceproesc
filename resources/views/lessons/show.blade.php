@extends('layouts.dashboard')

@section('title', 'Registro De Aula')

@section('content')

    <x-card.list.description-layout title="detalhes da aula">
        <x-slot name="items">
            <x-card.list.description-item label="instrutor" :description="$lesson->instructor"/>
            <x-card.list.description-item label="data" :description="$lesson->formatted_date"/>
            <x-card.list.description-item label="turma" :description="$lesson->class"/>
            <x-card.list.description-item label="disciplina" :description="$lesson->discipline"/>
            <x-card.list.description-item label="carga horária" :description="$lesson->hourly_load"/>
            <x-card.list.description-item label="registro" :description="$lesson->register"/>
        </x-slot>
    </x-card.list.description-layout>

    <x-card.list.table-layout title="lista de presença">

        <x-slot name="header">
            <x-card.list.table-header class="col-span-1" name="código"/>
            <x-card.list.table-header class="col-span-5" name="nome"/>
            <x-card.list.table-header class="col-span-2" name="turma"/>
            <x-card.list.table-header class="col-span-4" name="presença"/>
        </x-slot>

        <x-slot name="body">

            @foreach($lesson->novices as $novice)
            <x-card.list.table-row>
                <x-slot name="items">
                    <x-card.list.table-body-item class="col-span-1">
                        <x-slot name="item">
                            <span>123</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="col-span-5">
                        <x-slot name="item">
                            <span>{{ $novice->name }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="col-span-2">
                        <x-slot name="item">
                            <span>2021 - janeiro</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="col-span-4">
                        <x-slot name="item">
                            <span>{{ $novice->frequencyForLesson($lesson) }} hrs</span>
                        </x-slot>
                    </x-card.list.table-body-item>
                </x-slot>
            </x-card.list.table-row>
            @endforeach

        </x-slot>

    </x-card.list.table-layout>

@endsection
