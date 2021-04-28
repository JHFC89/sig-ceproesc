@extends('layouts.dashboard')

@section('title', 'Cadastrar Novo Coordenador')

@section('content')

    <x-card.form-layout 
        title="cadastrar novo coordenador" 
        :action="route('coordinators.store')"
        :single="false"
        method="POST"
    >

        <x-slot name="panels">

            <x-card.panel-layout title="cadastrar coordenador" class="px-6 py-2 divide-y">

                <x-slot name="content">

                    <x-card.form-input name="name" label="nome">
                        <x-slot name="input">
                            <input 
                                class="block w-full form-textarea @error('name') border-red-500 @enderror" 
                                name="name" 
                                value="{{ old('name') }}"
                                placeholder="Digite o nome do coordenador"
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
                                placeholder="Digite o e-mail do coordenador"
                            >
                            @error('email')
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
                                    placeholder="Digite o telefone do coordenador"
                                >
                                @error('phone')
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
                cadastrar coordenador
            </button>
        </x-slot>

    </x-card.form-layout>

@endsection
