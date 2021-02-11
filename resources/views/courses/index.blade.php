@extends('layouts.dashboard')

@section('title', 'Programas')

@section('content')

    <x-card.list.table-layout title="programas">

        <x-slot name="header">
            <x-card.list.table-header class="col-span-4" name="nome"/>
            <x-card.list.table-header class="col-span-4 text-center" name="carga horária"/>
            <x-card.list.table-header class="col-span-4" name=""/>
        </x-slot>

        <x-slot name="body">

            @foreach ($courses as $course)
            <x-card.list.table-row>
                <x-slot name="items">

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <span>{{ $course->name }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <div class="flex items-center justify-center h-full w-full">
                                <span>{{ $course->duration }} hrs</span>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <div class="flex justify-end space-x-2 w-full">
                                <a 
                                    href="{{ route('courses.show', ['course' => $course]) }}"
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
