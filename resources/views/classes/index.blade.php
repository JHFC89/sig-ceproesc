@extends('layouts.dashboard')

@section('title', 'Turmas')

@section('content')

    <x-card.list.table-layout title="turmas">

        <x-slot name="header">
            <x-card.list.table-header class="col-span-4" name="nome"/>
            <x-card.list.table-header class="col-span-4 text-center" name="cidade"/>
            <x-card.list.table-header class="col-span-4" name=""/>
        </x-slot>

        <x-slot name="body">

            @foreach ($courseClasses as $courseClass)
            <x-card.list.table-row>
                <x-slot name="items">

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <span>{{ $courseClass->name }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <div class="flex items-center justify-center h-full w-full">
                                <span>{{ $courseClass->city }}</span>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <div class="flex justify-end space-x-2 w-full">
                                <a 
                                    href="{{ route('classes.show', ['courseClass' => $courseClass]) }}"
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
