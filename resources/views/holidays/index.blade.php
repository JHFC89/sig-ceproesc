@extends('layouts.dashboard')

@section('title', 'Feriados')

@section('content')

    @if (session()->has('status'))
        <x-alert type="success" message="{{ session('status') }}"/>
    @endif

    <x-card.list.table-layout title="feriados">

        <x-slot name="header">
            <x-card.list.table-header class="col-span-4" name="nome"/>
            <x-card.list.table-header class="col-span-4" name="data"/>
        </x-slot>

        <x-slot name="body">

            @foreach ($holidays as $holiday)
            <x-card.list.table-row>
                <x-slot name="items">

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <span>{{ $holiday->name }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <span>{{ $holiday->formatted_date }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                </x-slot>
            </x-card.list.table-row>
            @endforeach

        </x-slot>

    </x-card.list.table-layout>

    <div class="flex justify-end">
        <a 
            href="{{ route('holidays.create') }}"
            class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
        >
            cadastrar feriados
        </a>
    </div>
@endsection
