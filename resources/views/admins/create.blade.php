@extends('layouts.dashboard')

@section('title', 'Cadastrar Novo Administrador')

@section('content')

    <x-card.form-layout 
        title="cadastrar novo administrador" 
        :action="route('admins.store')"
        :single="false"
        method="POST"
    >

        <x-slot name="panels">

            <x-card.panel-layout title="cadastrar administrador" class="px-6 py-2 divide-y">

                <x-slot name="content">

                    <x-card.form-input name="name" label="nome">
                        <x-slot name="input">
                            <input 
                                class="block w-full form-textarea @error('name') border-red-500 @enderror" 
                                name="name" 
                                value="{{ old('name') }}"
                                placeholder="Digite o nome do administrador"
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
                                placeholder="Digite o e-mail do administrador"
                            >
                    <x-validation-error name="email"/>
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
                cadastrar administrador
            </button>
        </x-slot>

    </x-card.form-layout>

@endsection
