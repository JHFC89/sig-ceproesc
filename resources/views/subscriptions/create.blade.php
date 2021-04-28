@extends('layouts.dashboard')

@section('title', 'Matricular Aprendizes')

@section('content')

    @if (session()->has('status'))
    <x-alert 
        type="success" 
        message="{{ session('status') }}" 
    />
    @endif

    @if (session()->has('error'))
    <x-alert 
        type="warning" 
        message="{{ session('error') }}" 
    />
    @endif

    @if (session()->has('no-lessons'))
    <x-alert 
        type="attention" 
        message="{{ session('no-lessons') }}" 
        actionText="Clique aqui para ver a turma"
        actionLink="{{ route('classes.show', ['courseClass' => $courseClass]) }}"
    />
    @elseif (session()->has('no-novices'))
    <x-alert 
        type="attention" 
        message="{{ session('no-novices') }}" 
    />
    @else
    <x-card.form-layout 
        title="matricular aprendizes" 
        :action="route('subscriptions.store')"
        method="POST"
    >

        <x-slot name="inputs">

            <x-card.form-input name="class" label="turma">
                <x-slot name="input">
                    <span 
                        class="block w-full font-medium" 
                    >
                        {{ $courseClass->name }}
                    </span>
                    <input type="hidden" name="class" value="{{ $courseClass->id }}">
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="novices" label="aprendizes">
                <x-slot name="input">
                    @foreach($availableNovices as $novice)
                        <div>
                            <label class="inline-flex items-center">
                                <input 
                                    type="checkbox"
                                    class="form-checkbox"
                                    name="novices[][id]"
                                    value="{{ $novice->id }}"
                                >
                                <span class="ml-2">{{ $novice->name }}</span>
                            </label>
                        </div>
                    @endforeach
                    @error('novices')
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
                cadastrar aprendizes
            </button>
        </x-slot>

    </x-card.form-layout>
    @endif

@endsection()
