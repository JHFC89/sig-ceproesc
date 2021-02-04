@extends('layouts.dashboard')

@section('title', 'Lista De Disciplinas')

@section('content')

    <x-card.list.table-layout title="lista de disciplinas">

        <x-slot name="header">
            <x-card.list.table-header class="col-span-4" name="nome"/>
            <x-card.list.table-header class="col-span-3 text-center" name="módulo"/>
            <x-card.list.table-header class="col-span-2 text-center" name="carga horária"/>
            <x-card.list.table-header class="col-span-3" name=""/>
        </x-slot>

        <x-slot name="body">

            @foreach ($disciplines as $discipline)
            <x-card.list.table-row>
                <x-slot name="items">

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <span>{{ $discipline->name }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-3">
                        <x-slot name="item">
                            <div class="flex items-center justify-center h-full w-full">
                                <span>{{ $discipline->type }}</span>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-2">
                        <x-slot name="item">
                            <div class="flex items-center justify-center h-full w-full">
                                <span>{{ $discipline->duration }} hrs</span>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-3">
                        <x-slot name="item">
                            <div class="flex justify-end space-x-2 w-full">
                                <a 
                                    href="{{ route('disciplines.edit', ['discipline' => $discipline]) }}"
                                    class="text-gray-300 hover:text-blue-300"
                                >
                                    <x-icons.edit class="w-6"/>
                                </a>
                                <a 
                                    href="{{ route('disciplines.show', ['discipline' => $discipline]) }}"
                                    class="text-gray-300 hover:text-blue-300"
                                >
                                    <x-icons.see class="w-6"/>
                                </a>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                </x-slot>
            </x-card.list.table-row>
            @endforeach

        </x-slot>

    </x-card.list.table-layout>
@endsection
