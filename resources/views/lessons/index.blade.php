@extends('layouts.dashboard')

@section('title', 'Aulas')

@section('content')

    <x-card.list.table-layout title="aulas">

        <x-slot name="header">
            <x-card.list.table-header class="col-span-1" name="data"/>
            <x-card.list.table-header class="col-span-1 text-center" name="horário"/>
            <x-card.list.table-header class="col-span-4 text-center" name="disciplina"/>
            <x-card.list.table-header class="col-span-4 text-center" name="instrutor"/>
            <x-card.list.table-header class="col-span-2" name=""/>
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

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <div class="w-full text-center">
                                <span>{{ $lesson->instructor->name }}</span>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-2">
                        <x-slot name="item">
                            <div class="flex justify-end space-x-2 w-full">
                                @can('update', $lesson)
                                <a 
                                    href="{{ route('lessons.edit', ['lesson' => $lesson]) }}"
                                    class="text-gray-300 hover:text-blue-300"
                                >
                                    <x-icons.edit class="w-6"/>
                                </a>
                                @endcan
                                <a 
                                    href="{{ route('lessons.show', ['lesson' => $lesson]) }}"
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

    {{ $lessons->links() }}

@endsection
