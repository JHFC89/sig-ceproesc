@extends('layouts.dashboard')

@section('title', 'Atualizar Disciplina')

@section('content')

    <x-card.form-layout 
        :title="'atualizar disciplina - ' . $discipline->name" 
        :action="route('disciplines.update', ['discipline' => $discipline])"
        method="PATCH"
    >
        <x-slot name="inputs">

            <x-card.form-input name="name" label="nome da disciplina">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('name') border-red-500 @enderror" 
                        name="name" 
                        placeholder="Digite o nome da disciplina"
                        value="{{ $discipline->name }}"
                    >
                    @error('name')
                        <span class="block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="basic" label="módulo">
                <x-slot name="input">
                    <div class="space-x-4">
                        <label class="inline-flex items-center space-x-2">
                            <input class="form-radio"
                                type="radio"
                                name="basic"
                                value="1"
                                @if ($discipline->isBasic()) checked @endif
                            >
                            <span>básico</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input
                                class="form-radio"
                                type="radio"
                                name="basic"
                                value="0"
                                @if ($discipline->isSpecific()) checked @endif
                            >
                            <span>específico</span>
                        </label>
                    </div>
                    @error('basic')
                        <span class="block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="duration" label="Carga Horária">
                <x-slot name="input">
                    <input 
                        class="block w-20 form-input @error('duration') border-red-500 @enderror" 
                        name="duration" 
                        type="number"
                        value="{{ $discipline->duration }}"
                    >
                    @error('duration')
                        <span class="block text-sm text-red-500">{{ $message }}</span>
                    @enderror
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
                                    @if ($discipline->isAttached($instructor))
                                        checked
                                    @endif
                                >
                                <span class="ml-2">{{ $instructor->name }}</span>
                            </label>
                        </div>
                    @endforeach
                    @error('instructors')
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
                atualizar disciplina
            </button>
        </x-slot>

    </x-card.form-layout>

@endsection()
