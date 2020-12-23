@extends('layouts.dashboard')

@section('title', 'Solicitação de Registro de Aula Vencida')

@section('content')

@if (session()->has('status'))
<x-alert 
    type="success" 
    message="{{ session('status') }}" 
/>
@endif

<x-card.list.description-layout title="detalhes da solicitação">
    <x-slot name="items">
        <x-card.list.description-item label="número" :description="$request->id"/>
        <x-card.list.description-item label="data" :description="$request->formatted_date"/>
        <x-card.list.description-item label="instrutor" :description="$request->lesson->instructor->name"/>
        <x-card.list.description-item label="justificativa" type="text" :description="$request->justification"/>
    </x-slot>
</x-card.list.description-layout>

<x-card.list.description-layout title="detalhes da aula">
    <x-slot name="items">
        <x-card.list.description-item label="instrutor" :description="$request->lesson->instructor->name"/>
        <x-card.list.description-item label="data" :description="$request->lesson->formatted_date"/>
        <x-card.list.description-item label="turma" :description="$request->lesson->formatted_course_classes"/>
    </x-slot>
</x-card.list.description-layout>

@can('update', $request)
<div class="flex justify-end">
    <form action="{{ route('requests.update', ['request' => $request]) }}" method="POST">
        @csrf
        @method('PATCH')
        <button class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown">liberar</button>
    </form>
</div>
@endcan

@endsection
