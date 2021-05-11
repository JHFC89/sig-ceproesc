@extends('layouts.dashboard')

@section('title', 'Frequência')

@section('content')

    <x-card.list.description-layout title="frequência">

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

            @if ($frequency)
            <x-card.list.description-item
                label="frequência"
                type="text"
                :description="$frequency . ' %'"
            />

            @else
            <x-card.list.description-item
                label="frequência"
                type="text"
                description="Ainda não há aulas registradas."
            />
            @endif

        </x-slot>

    </x-card.list.description-layout>

    <x-card.list.table-layout title="aulas" class="hidden lg:block">

        <x-slot name="header">
            <x-card.list.table-header class="col-span-1" name="data"/>
            <x-card.list.table-header class="col-span-1 text-center" name="horário"/>
            <x-card.list.table-header class="col-span-4 text-center" name="disciplina"/>
            <x-card.list.table-header class="col-span-3 text-center" name="registrada"/>
            <x-card.list.table-header class="col-span-3 text-center" name="presença"/>
        </x-slot>

        <x-slot name="body">

            @foreach ($lessons as $lesson)
            <x-card.list.table-row>
                <x-slot name="items">

                    <x-card.list.table-body-item class="flex items-center col-span-1">
                        <x-slot name="item">
                            <span>{{ $lesson->formattedDate }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-1">
                        <x-slot name="item">
                            <div class="w-full text-center">
                                <span>{{ $lesson->formattedType }}</span>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <div class="w-full text-center">
                                <span>{{ $lesson->discipline->name }}</span>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="col-span-3">
                        <x-slot name="item">
                            <div class="flex items-center justify-center h-full">
                                <x-icons.active class="w-2 h-2" :active="$lesson->isRegistered()"/>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="col-span-3">
                        <x-slot name="item">
                            <div class="flex items-center justify-center h-full">
                                @if ($lesson->isRegistered())
                                <x-icons.active class="w-2 h-2" :active="$lesson->isPresent($registration->user)"/>
                                @else
                                <span class="block w-2 h-2 rounded-full bg-gray-300"></span>
                                @endif
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                </x-slot>
            </x-card.list.table-row>
            @endforeach

        </x-slot>

    </x-card.list.table-layout>

    <div class="lg:hidden">

        <div class="mb-4">
            {{ $lessons->links() }}
        </div>

        <x-card.list.description-layout title="aulas">


            <x-slot name="items">


                @foreach($lessons as $lesson)
                <div class="pt-8 pb-2 font-medium text-center">
                    <a href="{{ route('lessons.show', ['lesson' => $lesson]) }}" class="inline-block pr-2 font-medium text-blue-500 normal-case underline hover:text-blue-700">
                        Aula: {{ $lesson->formatted_date }} - {{ $lesson->formattedType }} horário
                    </a>
                </div>
                <x-card.list.description-item
                    label="disciplina"
                    :description="$lesson->discipline->name"
                />
                <x-card.list.description-item
                    label="instrutor"
                    :description="$lesson->instructor->name"
                />
                @if ($lesson->isRegistered())
                <x-card.list.description-item
                    label="registrada"
                    description="sim"
                />
                <x-card.list.description-item
                    label="presença"
                    :description="$lesson->isPresent($registration->user) ? 'presente' : 'ausente'"
                />
                @else
                <x-card.list.description-item
                    label="registrada"
                    description="não"
                />
                @endif
                @endforeach

            </x-slot>

        </x-card.list.description-layout>
    </div>

    {{ $lessons->links() }}

@endsection
