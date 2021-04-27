@extends('layouts.dashboard')

@section('title', 'Cadastrar Novo Aprendiz')

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

    <x-card.form-layout 
            title="cadastrar novo aprendiz" 
            :action="route('companies.novices.store', ['company' => $company])"
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
                                    placeholder="Digite o nome do aprendiz"
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
                                    placeholder="Digite o e-mail do aprendiz"
                                >
                                @error('email')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
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
                                @error('birthdate')
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
                                    placeholder="Digite o RG do aprendiz"
                                >
                                @error('rg')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </x-slot>
                        </x-card.form-input>

                        <x-card.form-input name="cpf" label="CPF">
                            <x-slot name="input">
                                <input 
                                    class="block w-full form-textarea @error('cpf') border-red-500 @enderror" 
                                    name="cpf" 
                                    value="{{ old('cpf') }}"
                                    placeholder="Digite o CPF do aprendiz (123.123.123-12)"
                                >
                                @error('cpf')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </x-slot>
                        </x-card.form-input>

                        <x-card.form-input name="ctps" label="CTPS">
                            <x-slot name="input">
                                <input 
                                    class="block w-full form-textarea @error('ctps') border-red-500 @enderror" 
                                    name="ctps" 
                                    value="{{ old('ctps') }}"
                                    placeholder="Digite a CTPS do aprendiz"
                                >
                                @error('ctps')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </x-slot>
                        </x-card.form-input>

                        <x-card.form-input name="phone" label="Telefone">
                                <x-slot name="input">
                                    <input 
                                        class="block w-full form-textarea @error('phone') border-red-500 @enderror" 
                                        name="phone" 
                                        value="{{ old('phone') }}"
                                        placeholder="Digite o telefone do aprendiz"
                                    >
                                    @error('phone')
                                        <span class="block text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                </x-slot>
                        </x-card.form-input>

                        <x-card.form-input name="responsable_name" label="nome do responsável">
                                <x-slot name="input">
                                    <input 
                                        class="block w-full form-textarea @error('responsable_name') border-red-500 @enderror" 
                                        name="responsable_name" 
                                        value="{{ old('responsable_name') }}"
                                        placeholder="Digite o nome do responsável pelo aprendiz"
                                    >
                                    @error('responsable_name')
                                        <span class="block text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                </x-slot>
                        </x-card.form-input>

                        <x-card.form-input name="responsable_cpf" label="CPF do responsável">
                                <x-slot name="input">
                                    <input 
                                        class="block w-full form-textarea @error('responsable_cpf') border-red-500 @enderror" 
                                        name="responsable_cpf" 
                                        value="{{ old('responsable_cpf') }}"
                                        placeholder="Digite o cpf do responsável pelo aprendiz"
                                    >
                                    @error('responsable_cpf')
                                        <span class="block text-sm text-red-500">{{ $message }}</span>
                                    @enderror
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
                                    placeholder="Digite o logradouro do endereço do aprendiz"
                                >
                                @error('address.street')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </x-slot>
                        </x-card.form-input>

                        <x-card.form-input name="address.number" label="número">
                            <x-slot name="input">
                                <input 
                                    class="block w-full form-textarea @error('address.number') border-red-500 @enderror" 
                                    name="address[number]" 
                                    value="{{ old('address.number') }}"
                                    placeholder="Digite o número do endereço do aprendiz"
                                >
                                @error('address.number')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </x-slot>
                        </x-card.form-input>

                        <x-card.form-input name="address.district" label="bairro">
                            <x-slot name="input">
                                <input 
                                    class="block w-full form-textarea @error('address.district') border-red-500 @enderror" 
                                    name="address[district]" 
                                    value="{{ old('address.district') }}"
                                    placeholder="Digite o bairro do endereço do aprendiz"
                                >
                                @error('address.district')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </x-slot>
                        </x-card.form-input>

                        <x-card.form-input name="address.city" label="cidade">
                            <x-slot name="input">
                                <input 
                                    class="block w-full form-textarea @error('address.city') border-red-500 @enderror" 
                                    name="address[city]" 
                                    value="{{ old('address.city') }}"
                                    placeholder="Digite a cidade do endereço do aprendiz"
                                >
                                @error('address.city')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </x-slot>
                        </x-card.form-input>

                        <x-card.form-input name="address.cep" label="CEP">
                            <x-slot name="input">
                                <input 
                                    class="block w-full form-textarea @error('address.cep') border-red-500 @enderror" 
                                    name="address[cep]" 
                                    value="{{ old('address.cep') }}"
                                    placeholder="Digite o CEP do endereço do aprendiz (12.123-123)"
                                >
                                @error('address.cep')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </x-slot>
                        </x-card.form-input>

                        <x-card.form-input name="address.state" label="estado">
                            <x-slot name="input">
                                <input 
                                    class="block w-full form-textarea @error('address.state') border-red-500 @enderror" 
                                    name="address[state]" 
                                    value="{{ old('address.state') }}"
                                    placeholder="Digite o estado do endereço do aprendiz"
                                >
                                @error('address.state')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </x-slot>
                        </x-card.form-input>

                        <x-card.form-input name="address.country" label="país">
                            <x-slot name="input">
                                <input 
                                    class="block w-full form-textarea @error('address.country') border-red-500 @enderror" 
                                    name="address[country]" 
                                    value="{{ old('address.country') }}"
                                    placeholder="Digite o país do endereço do aprendiz"
                                >
                                @error('address.country')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
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
                    cadastrar aprendiz
                </button>
            </x-slot>

        </x-card.form-layout>
@endsection
