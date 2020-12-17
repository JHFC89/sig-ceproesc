@extends('layouts.dashboard')

@section('title', 'Solicitação de Liberação de Aula Vencida')

@section('content')
    <x-card.list.description-layout title="detalhes da aula">
        <x-slot name="items">
            <x-card.list.description-item label="instrutor" :description="$lesson->instructor->name"/>
            <x-card.list.description-item label="data" :description="$lesson->formatted_date"/>
            <x-card.list.description-item label="turma" :description="$lesson->formatted_course_classes"/>
            <x-card.list.description-item label="disciplina" :description="$lesson->discipline"/>
            <x-card.list.description-item label="carga horária" :description="$lesson->hourly_load"/>
        </x-slot>
    </x-card.list.description-layout>

    <x-card.form-layout 
        title="solicitação de liberação de aula vencida" 
        :action="route('lessons.requests.store', ['lesson' => $lesson])"
        method="POST"
    >

        <x-slot name="inputs">

            <x-card.form-input name="justification" label="justificativa">
                <x-slot name="input">
                    <textarea 
                        class="block w-full form-textarea @error('justification') border-red-500 @enderror" 
                        name="justification" 
                        rows="4"
                        placeholder="Digite aqui a justificativa do atraso para registrar a aula"
                    ></textarea>
                    @error('justification')
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
                enviar solicitação
            </button>
        </x-slot>

    </x-card.form-layout>
@endsection

