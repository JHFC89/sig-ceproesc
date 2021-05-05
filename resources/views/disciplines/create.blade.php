@extends('layouts.dashboard')

@section('title', 'Registro De Aula')

@section('content')

    <x-card.form-layout 
        title="cadastrar nova disciplina" 
        :action="route('disciplines.store')"
        method="POST"
    >

        <x-slot name="inputs">

            <x-card.form-input name="name" label="nome da disciplina">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('name') border-red-500 @enderror" 
                        name="name" 
                        placeholder="Digite o nome da disciplina"
                    >
                    <x-validation-error name="name"/>
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="basic" label="módulo">
                <x-slot name="input">
                    <div class="space-x-4">
                        <label class="inline-flex items-center space-x-2">
                            <input class="form-radio" type="radio" name="basic" value="1">
                            <span>básico</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input class="form-radio" type="radio" name="basic" value="0">
                            <span>específico</span>
                        </label>
                    </div>
                    <x-validation-error name="basic"/>
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="duration" label="Carga Horária">
                <x-slot name="input">
                    <input 
                        class="block w-20 form-input @error('duration') border-red-500 @enderror" 
                        name="duration" 
                        type="number"
                    >
                    <x-validation-error name="duration"/>
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="instructors" label="Instrutores">
                <x-slot name="input">
                    @foreach($instructors as $instructor)
                        <div>
                            <label class="inline-flex items-center">
                                <input 
                                    type="checkbox"
                                    class="form-checkbox"
                                    name="instructors[]"
                                    value="{{ $instructor->id }}"
                                >
                                <span class="ml-2">{{ $instructor->name }}</span>
                            </label>
                        </div>
                    @endforeach
                    <x-validation-error name="instructors"/>
                </x-slot>
            </x-card.form-input>
        </x-slot>

        <x-slot name="footer">
            <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                cadastrar disciplina
            </button>
        </x-slot>

    </x-card.form-layout>

@endsection()
