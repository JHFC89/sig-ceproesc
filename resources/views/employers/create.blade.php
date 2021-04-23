@extends('layouts.dashboard')

@section('title', 'Cadastrar Novo Representante')

@section('content')

    <x-card.list.description-layout title="empresa">
        <x-slot name="items">
            <x-card.list.description-item
                label="razão social"
                :description="$company->name"
            />
        </x-slot>
    </x-card.list.description-layout>

    <x-card.form-layout 
        title="cadastrar novo representante" 
        :action="route('companies.employers.store', ['company' => $company])"
        method="POST"
    >

        <x-slot name="inputs">

            <x-card.form-input name="name" label="nome">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('name') border-red-500 @enderror" 
                        name="name" 
                        value="{{ old('name') }}"
                        placeholder="Digite o nome do representante"
                    >
                    @error('name')
                        <span class="block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="email" label="e-mail">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('email') border-red-500 @enderror" 
                        name="email" 
                        value="{{ old('email') }}"
                        placeholder="Digite o e-mail do representante"
                    >
                    @error('email')
                        <span class="block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="rg" label="RG">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('rg') border-red-500 @enderror" 
                        name="rg" 
                        value="{{ old('rg') }}"
                        placeholder="Digite o RG do representante"
                    >
                    @error('rg')
                        <span class="block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

        </x-slot>

        <x-slot name="footer">
            <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                cadastrar representante
            </button>
        </x-slot>

    </x-card.form-layout>

@endsection
