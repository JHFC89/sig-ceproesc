@extends('layouts.dashboard')

@section('title', 'Alterar aula')

@section('content')

    @if (session()->has('status'))
    <x-alert 
        type="success" 
        message="{{ session('status') }}" 
    />
    @endif

    @error ('*')
    <x-alert 
        type="warning" 
        message="Ocorreu um erro!" 
    />
    <div class="space-y-1">
        @foreach ($errors->all() as $error)
        <x-alert 
            type="warning" 
            :message="$error" 
        />
        @endforeach
    </div>
    @enderror

    <x-card.form-layout 
        title="alterar data da aula" 
        :action="route('lesson-dates.update', ['lesson' => $lesson])"
        method="PATCH"
    >

        <x-slot name="inputs">

            <x-card.form-input name="date" label="data de nascimento">
                <x-slot name="input">
                    <x-card.select-date
                        dayName="date[day]"
                        :dayValue="old('date.day', $lesson->date->day)"
                        monthName="date[month]"
                        :monthValue="old('date.month', $lesson->date->month)"
                        yearName="date[year]"
                        :yearValue="old('date.year', $lesson->date->year)"
                        :minYear="false"
                    />
                    <x-validation-error name="date"/>
                    <x-validation-error name="date.day"/>
                    <x-validation-error name="date.month"/>
                    <x-validation-error name="date.year"/>
                </x-slot>
            </x-card.form-input>

        </x-slot>

        <x-slot name="footer">
            <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                alterar
            </button>
        </x-slot>

    </x-card.form-layout>



    <x-card.form-layout 
        title="alterar instrutor da aula" 
        :action="route('lesson-instructors.update', ['lesson' => $lesson])"
        method="PATCH"
    >

        <x-slot name="inputs">

            <x-card.form-input name="discipline" label="disciplina">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea bg-gray-100" 
                        value="{{ $lesson->discipline->name }}"
                        disabled
                    >
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="current_instructor" label="instrutor atual">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea bg-gray-100" 
                        value="{{ $lesson->instructor->name }}"
                        disabled
                    >
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="instructors" label="novo instrutor">
                <x-slot name="input">
                    @foreach($lesson->discipline->instructors as $instructor)
                        <div>
                            <label class="inline-flex items-center">
                                <input 
                                    @if ($lesson->instructor->is($instructor))
                                    checked
                                    @endif
                                    type="radio"
                                    class="form-radio"
                                    name="instructor"
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
                alterar
            </button>
        </x-slot>

    </x-card.form-layout>

@endsection
