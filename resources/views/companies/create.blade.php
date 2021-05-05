@extends('layouts.dashboard')

@section('title', 'Cadastrar Nova Empresa')

@section('content')

    <x-card.form-layout 
        x-data="form()"
        title="cadastrar nova empresa" 
        :action="route('companies.store')"
        method="POST"
    >

        <x-slot name="inputs">

            <x-card.form-input name="name" label="razão social">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('name') border-red-500 @enderror" 
                        name="name" 
                        value="{{ old('name') }}"
                        placeholder="Digite a razão social da empresa"
                    >
                    <x-validation-error name="name"/>
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="cnpj" label="CNPJ">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('cnpj') border-red-500 @enderror" 
                        name="cnpj" 
                        value="{{ old('cnpj') }}"
                        placeholder="Digite o CNPJ (12.123.123/0001-12)"
                    >
                    <x-validation-error name="cnpj"/>
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="phone" label="Telefone">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('phone') border-red-500 @enderror" 
                        name="phone" 
                        value="{{ old('phone') }}"
                        placeholder="Digite o telefone da empresa"
                    >
                    <x-validation-error name="phone"/>
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.street" label="logradouro">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('street') border-red-500 @enderror" 
                        name="address[street]" 
                        value="{{ old('address.street') }}"
                        placeholder="Digite o logradouro do endereço da empresa"
                    >
                    <x-validation-error name="address.street"/>
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.number" label="número">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('address.number') border-red-500 @enderror" 
                        name="address[number]" 
                        value="{{ old('address.number') }}"
                        placeholder="Digite o número do endereço da empresa"
                    >
                    <x-validation-error name="address.number"/>
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.district" label="bairro">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('address.district') border-red-500 @enderror" 
                        name="address[district]" 
                        value="{{ old('address.district') }}"
                        placeholder="Digite o bairro do endereço da empresa"
                    >
                    <x-validation-error name="address.district"/>
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.city" label="cidade">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('address.city') border-red-500 @enderror" 
                        name="address[city]" 
                        value="{{ old('address.city') }}"
                        placeholder="Digite a cidade do endereço da empresa"
                    >
                    <x-validation-error name="address.city"/>
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.cep" label="CEP">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('address.cep') border-red-500 @enderror" 
                        name="address[cep]" 
                        value="{{ old('address.cep') }}"
                        placeholder="Digite o CEP do endereço da empresa (12.123-123)"
                    >
                    <x-validation-error name="address.cep"/>
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.state" label="estado">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('address.state') border-red-500 @enderror" 
                        name="address[state]" 
                        value="{{ old('address.state') }}"
                        placeholder="Digite o estado do endereço da empresa"
                    >
                    <x-validation-error name="address.state"/>
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.country" label="país">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('address.country') border-red-500 @enderror" 
                        name="address[country]" 
                        value="{{ old('address.country') }}"
                        placeholder="Digite o país do endereço da empresa"
                    >
                    <x-validation-error name="address.country"/>
                </x-slot>
            </x-card.form-input>

        </x-slot>

        <x-slot name="footer">
            <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                cadastrar empresa
            </button>
        </x-slot>

    </x-card.form-layout>

@endsection
