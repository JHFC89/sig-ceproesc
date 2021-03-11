@extends('layouts.dashboard')

@section('title', 'Registro De Aula')

@section('content')

    <x-card.form-layout 
        x-data="form()"
        title="cadastrar novo programa" 
        :action="route('courses.store')"
        method="POST"
    >

        <x-slot name="inputs">

            <x-card.form-input name="name" label="nome">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('name') border-red-500 @enderror" 
                        name="name" 
                        placeholder="Digite o nome do programa"
                    >
                    @error('name')
                        <span class="block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="duration" label="carga horária total">
                <x-slot name="input">
                    <input 
                        class="inline-block w-20 form-input @error('duration') border-red-500 @enderror" 
                        name="duration" 
                        type="number"
                        x-model.number="data.totalDuration"
                    >
                    <div class="inline-block text-red-500 pl-2 text-sm">
                        <span x-show="data.durationDiff > 0">
                            *Faltam <span x-text="data.durationDiff"></span> horas</span>
                        </span>
                        <span x-show="data.durationDiff < 0">
                            *Sobraram <span x-text="data.durationDiff * -1"></span> horas</span>
                        </span>
                    </div>
                    @error('duration')
                        <span class="block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="basic disciplines duration" label="carga horária básico">
                <x-slot name="input">
                    <input 
                        class="block w-20 form-input bg-gray-100" 
                        type="number"
                        :value="data.basicDuration"
                        disabled
                    >
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="specific disciplines duration" label="carga horária específico">
                <x-slot name="input">
                    <input 
                        disabled
                        class="block w-20 form-input bg-gray-100" 
                        type="number"
                        value="90"
                        :value="data.specificDuration"
                        disabled
                    >
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="disciplines" label="disciplinas módulo básico">
                <x-slot name="input">
                    @foreach($basic_disciplines as $discipline)
                        <div>
                            <label class="inline-flex items-center">
                                <input 
                                    type="checkbox"
                                    class="form-checkbox"
                                    name="disciplines[]"
                                    value="{{ $discipline->id }}"
                                    @change="addToDuration($event.target.checked, {{ $discipline->duration }})"
                                >
                                <span class="ml-2">
                                    {{ $discipline->name }} - 
                                    <strong>{{ $discipline->duration }} hr</strong>
                                </span>
                            </label>
                        </div>
                    @endforeach
                    @error('disciplines')
                        <span class="block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="disciplines" label="disciplinas módulo específico">
                <x-slot name="input">
                    @foreach($specific_disciplines as $discipline)
                        <div>
                            <label class="inline-flex items-center">
                                <input 
                                    type="checkbox"
                                    class="form-checkbox"
                                    name="disciplines[]"
                                    value="{{ $discipline->id }}"
                                    @change="addToDuration($event.target.checked, {{ $discipline->duration }}, true)"
                                >
                                <span class="ml-2">
                                    {{ $discipline->name }} - 
                                    <strong>{{ $discipline->duration }} hr</strong>
                                </span>
                            </label>
                        </div>
                    @endforeach
                    @error('disciplines')
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
                cadastrar programa
            </button>
        </x-slot>

    </x-card.form-layout>

@endsection()

@section('scripts')

    @parent

    <script>
        function form() {
            return {
                data: {
                    totalDuration: null,
                    currentDuration: null,
                    basicDuration: null,
                    specificDuration: null,
                    durationDiff: 0,
                },
                addToDuration(checked, duration, specific = false) {
                    value = checked ? duration : - duration;
                    if (specific) {
                        this.data.specificDuration += value;
                    } else {
                        this.data.basicDuration += value;
                    }
                    this.data.currentDuration += value;
                    this.updateDurationDiff();
                },
                updateDurationDiff() {
                    this.data.durationDiff = this.data.totalDuration - this.data.currentDuration;
                }
            } 
        }
    </script>
@endsection
