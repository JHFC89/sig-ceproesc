@extends('layouts.dashboard')

@section('title', 'Aprendizes')

@section('content')

    <x-card.list.description-layout title="empresa">

        <x-slot name="items">
            <x-card.list.description-item
                type="link"
                :href="route('companies.show', ['company' => $company])"
                label="razão social"
                :description="$company->name"
            />
        </x-slot>

    </x-card.list.description-layout>

    <x-card.list.table-layout title="aprendizes">

        <x-slot name="header">
            <x-card.list.table-header class="col-span-4" name="nome"/>
            <x-card.list.table-header class="col-span-4" name="e-mail"/>
            <x-card.list.table-header class="col-span-2 text-center" name="registrado"/>
            <x-card.list.table-header class="col-span-2" name=""/>
        </x-slot>

        <x-slot name="body">

            @foreach ($registrations as $registration)
            <x-card.list.table-row>
                <x-slot name="items">

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <span>{{ $registration->name }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <span class="normal-case">{{ $registration->email }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-2">
                        <x-slot name="item">
                            <div class="flex items-center justify-center h-full w-full">
                                <span>{{ $registration->invitation->hasBeenUsed() ? 'sim' : 'não' }}</span>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-2">
                        <x-slot name="item">
                            <div class="flex justify-end space-x-2 w-full">
                                <a 
                                    href="{{ route('novices.show', ['registration' => $registration]) }}"
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
