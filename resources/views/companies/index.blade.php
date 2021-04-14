@extends('layouts.dashboard')

@section('title', 'Empresas')

@section('content')

    <x-card.list.table-layout title="empresas">

        <x-slot name="header">
            <x-card.list.table-header class="col-span-8" name="nome"/>
            <x-card.list.table-header class="col-span-4" name=""/>
        </x-slot>

        <x-slot name="body">

            @foreach ($companies as $company)
            <x-card.list.table-row>
                <x-slot name="items">

                    <x-card.list.table-body-item class="flex items-center col-span-8">
                        <x-slot name="item">
                            <span>{{ $company->name }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <div class="flex justify-end space-x-2 w-full">
                                <a 
                                    href="{{ route('companies.show', ['company' => $company]) }}"
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
