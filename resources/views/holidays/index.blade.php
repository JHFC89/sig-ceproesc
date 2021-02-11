@extends('layouts.dashboard')

@section('title', 'Feriados')

@section('content')

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
@endsection
