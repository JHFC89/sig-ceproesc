@extends('layouts.dashboard')

@section('title', 'Cadastrar Novo Instrutor')

@section('content')

    <x-card.form-layout 
        title="cadastrar novo instrutor" 
        :action="route('instructors.store')"
        :single="false"
        method="POST"
    >

        <x-slot name="panels">

            <x-card.panel-layout title="dados pessoais" class="px-6 py-2 divide-y">

                <x-slot name="content">

                    <x-card.form-input name="name" label="nome">
                        <x-slot name="input">
                            <input 
                                class="block w-full form-textarea @error('name') border-red-500 @enderror" 
                                name="name" 
                                value="{{ old('name') }}"
                                placeholder="Digite o nome do instrutor"
                            >
                    <x-validation-error name="name"/>
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="email" label="e-mail">
                        <x-slot name="input">
                            <input 
                                class="block w-full form-textarea @error('email') border-red-500 @enderror" 
                                name="email" 
                                value="{{ old('email') }}"
                                placeholder="Digite o e-mail do instrutor"
                            >
                    <x-validation-error name="email"/>
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="birthdate" label="data de nascimento">
                        <x-slot name="input">
                            <x-card.select-date
                                dayName="birthdate[day]"
                                :dayValue="old('birthdate.day') ?? 1"
                                monthName="birthdate[month]"
                                :monthValue="old('birthdate.month') ?? 1"
                                yearName="birthdate[year]"
                                :yearValue="old('birthdate.year') ?? now()->format('Y')"
                                :minYear="false"
                            />
                    <x-validation-error name="birthdate"/>
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="rg" label="RG">
                        <x-slot name="input">
                            <input 
                                class="block w-full form-textarea @error('rg') border-red-500 @enderror" 
                                name="rg" 
                                value="{{ old('rg') }}"
                                placeholder="Digite o RG do instrutor"
                            >
                    <x-validation-error name="rg"/>
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="cpf" label="CPF">
                        <x-slot name="input">
                            <input 
                                class="block w-full form-textarea @error('cpf') border-red-500 @enderror" 
                                name="cpf" 
                                value="{{ old('cpf') }}"
                                placeholder="Digite o CPF do instrutor (123.123.123-12)"
                            >
                    <x-validation-error name="cpf"/>
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="ctps" label="CTPS">
                        <x-slot name="input">
                            <input 
                                class="block w-full form-textarea @error('ctps') border-red-500 @enderror" 
                                name="ctps" 
                                value="{{ old('ctps') }}"
                                placeholder="Digite a CTPS do instrutor"
                            >
                    <x-validation-error name="ctps"/>
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="phone" label="Telefone">
                            <x-slot name="input">
                                <input 
                                    class="block w-full form-textarea @error('phone') border-red-500 @enderror" 
                                    name="phone" 
                                    value="{{ old('phone') }}"
                                    placeholder="Digite o telefone do instrutor"
                                >
                    <x-validation-error name="phone"/>
                            </x-slot>
                    </x-card.form-input>

                </x-slot>

            </x-card.panel-layout>

            <x-card.panel-layout title="endereço" class="px-6 py-2 divide-y">

                <x-slot name="content">

                    <x-card.form-input name="address.street" label="logradouro">
                        <x-slot name="input">
                            <input 
                                class="block w-full form-textarea @error('street') border-red-500 @enderror" 
                                name="address[street]" 
                                value="{{ old('address.street') }}"
                                placeholder="Digite o logradouro do endereço do instrutor"
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
                                placeholder="Digite o número do endereço do instrutor"
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
                                placeholder="Digite o bairro do endereço do instrutor"
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
                                placeholder="Digite a cidade do endereço do instrutor"
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
                                placeholder="Digite o CEP do endereço do instrutor (12.123-123)"
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
                                placeholder="Digite o estado do endereço do instrutor"
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
                                placeholder="Digite o país do endereço do instrutor"
                            >
                    <x-validation-error name="address.country"/>
                        </x-slot>
                    </x-card.form-input>

                </x-slot>

            </x-card.panel-layout>

        </x-slot>

        <x-slot name="footer">
            <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                cadastrar instrutor
            </button>
        </x-slot>

    </x-card.form-layout>

@endsection
