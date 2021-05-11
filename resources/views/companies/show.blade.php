@extends('layouts.dashboard')

@section('title', 'Empresa')

@section('content')

    @if (session()->has('status'))
    <x-alert 
        type="success" 
        message="{{ session('status') }}" 
    />
    @endif

    <x-card.list.description-layout title="empresa">
        <x-slot name="items">
            <x-card.list.description-item
                label="razão social"
                :description="$company->name"
            />
            <x-card.list.description-item
                label="CNPJ"
                :description="$company->cnpj"
            />
            <x-card.list.description-item
                label="telefone"
                :description="$company->phones[0]->number"
            />
            <x-card.list.description-item
                label="rua"
                :description="$company->address->street . ', ' . $company->address->number"
            />
            <x-card.list.description-item
                label="bairro"
                :description="$company->address->district"
            />
            <x-card.list.description-item
                label="cidade"
                :description="$company->address->city . '/' . $company->address->state"
            />
            <x-card.list.description-item
                label="CEP"
                :description="$company->address->cep"
            />
            <x-card.list.description-item
                label="país"
                :description="$company->address->country"
            />
        </x-slot>
    </x-card.list.description-layout>

    <div class="flex flex-col space-y-3 justify-end text-center mt-4 lg:flex-row lg:space-y-0 lg:space-x-2">
        @can('viewAny', App\Models\Company::class)
        <a
            href="{{ route('companies.employers.index', ['company' => $company]) }}"
            class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
        >
            ver representantes
        </a>
        @endcan
        @can('create', App\Models\Company::class)
        <a
            href="{{ route('companies.employers.create', ['company' => $company]) }}"
            class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
        >
            cadastrar representante
        </a>
        @endcan
        @can('viewAny', [App\Models\Registration::class, $company])
        <a
            href="{{ route('companies.novices.index', ['company' => $company]) }}"
            class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
        >
            ver apredizes
        </a>
        @endcan
        @can('create', App\Models\Registration::class)
        <a
            href="{{ route('companies.novices.create', ['company' => $company]) }}"
            class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
        >
            cadastrar aprediz
        </a>
        @endcan
    </div>
@endsection
