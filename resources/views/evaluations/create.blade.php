@extends('layouts.dashboard')

@section('title', 'Registro De Aula')

@section('content')

    <x-card.list.description-layout title="detalhes da aula">
        <x-slot name="items">
            <x-card.list.description-item label="data" :description="$lesson->formatted_date"/>
            <x-card.list.description-item label="turma" :description="$lesson->formatted_course_classes"/>
            <x-card.list.description-item label="disciplina" :description="$lesson->discipline"/>
            <x-card.list.description-item label="instrutor" :description="$lesson->instructor->name"/>
            <x-card.list.description-item label="carga horária" :description="$lesson->hourly_load"/>
        </x-slot>
    </x-card.list.description-layout>

    <x-card.form-layout 
        title="atividade avaliativa" 
        :action="route('lessons.evaluations.store', ['lesson' => $lesson])"
        method="POST"
    >

        <x-slot name="inputs">

            <x-card.form-input name="label" label="nome da atividade">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('label') border-red-500 @enderror" 
                        name="label" 
                        rows="4"
                        placeholder="Digite a descrição da atividade"
                    >
                    @error('label')
                    <span class="block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="description" label="descrição">
                <x-slot name="input">
                    <textarea 
                        class="block w-full form-textarea @error('description') border-red-500 @enderror" 
                        name="description" 
                        rows="4"
                        placeholder="Digite a descrição da atividade"
                    ></textarea>
                    @error('description')
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
                criar atividade
            </button>
        </x-slot>

    </x-card.form-layout>

@endsection()
