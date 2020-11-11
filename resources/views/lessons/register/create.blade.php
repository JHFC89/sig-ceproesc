@extends('layouts.dashboard')

@section('title', 'Registro De Aula')

@section('content')

    <x-card.list.description-layout title="detalhes da aula">
        <x-slot name="items">
            <x-card.list.description-item label="data" :description="$lesson->formatted_date"/>
            <x-card.list.description-item label="turma" :description="$lesson->class"/>
            <x-card.list.description-item label="disciplina" :description="$lesson->discipline"/>
            <x-card.list.description-item label="instrutor" :description="$lesson->instructor"/>
            <x-card.list.description-item label="carga horária" :description="$lesson->hourly_load"/>
        </x-slot>
    </x-card.list.description-layout>

    <x-card.list.table-layout title="lista de presença">
        <x-slot name="header">
            <x-card.list.table-header class="col-span-1" name="código"/>
            <x-card.list.table-header class="col-span-5" name="nome"/>
            <x-card.list.table-header class="col-span-1" name="turma"/>
            <x-card.list.table-header class="col-span-5" name="presença"/>
        </x-slot>

        <x-slot name="body">

            @foreach($lesson->novices as $novice)
            <x-card.list.table-row>
                <x-slot name="items">
                    <x-card.list.table-body-item class="col-span-1">
                        <x-slot name="item">
                            <span>123</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="col-span-5">
                        <x-slot name="item">
                            <span>{{ $novice->name }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="col-span-1">
                        <x-slot name="item">
                            <span>2021 - janeiro</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="col-span-5">
                        <x-slot name="item">
                            <div x-data class="space-x-4">
                                <label class="inline-flex items-center space-x-2"> 
                                    <input 
                                        @change="$dispatch('frequency-event', {'{{ $novice->id }}' : 0})" 
                                        class="form-radio" 
                                        type="radio" 
                                        name="presence-{{ $novice->id }}" 
                                        value="0"
                                        {{ $novice->lessons->find($lesson)->presence->frequency === 0 ? 'checked' : '' }}
                                    >
                                    <span>0</span>
                                </label>
                                <label class="inline-flex items-center space-x-2">
                                    <input 
                                        @change="$dispatch('frequency-event', {'{{ $novice->id }}' : 1})" 
                                        class="form-radio" 
                                        type="radio" 
                                        name="presence-{{ $novice->id }}" 
                                        value="1"
                                        {{ $novice->lessons->find($lesson)->presence->frequency == 1 ? 'checked' : '' }}
                                    >
                                    <span>1</span>
                                </label>
                                <label class="inline-flex items-center space-x-2">
                                    <input 
                                        @change="$dispatch('frequency-event', {'{{ $novice->id }}' : 2})" 
                                        class="form-radio" 
                                        type="radio" 
                                        name="presence-{{ $novice->id }}" 
                                        value="2"
                                        {{ $novice->lessons->find($lesson)->presence->frequency == 2 ? 'checked' : '' }}
                                    >
                                    <span>2</span>
                                </label>
                                <label class="inline-flex items-center space-x-2">
                                    <input 
                                        @change="$dispatch('frequency-event', {'{{ $novice->id }}' : 3})" 
                                        class="form-radio" 
                                        type="radio" 
                                        name="presence-{{ $novice->id }}" 
                                        value="3" 
                                        {{ $novice->lessons->find($lesson)->presence->frequency == 3 ? 'checked' : '' }}
                                        {{ $novice->lessons->find($lesson)->presence->frequency === null ? 'checked' : '' }}
                                    >
                                    <span>3</span>
                                </label>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>
                </x-slot>
            </x-card.list.table-row>
            @endforeach

        </x-slot>

    </x-card.list.table-layout>

    <x-card.form-layout x-data="form()" @frequency-event.window="changeFrequency($event.detail)" title="registro da aula">

        <x-slot name="inputs">

            <x-card.form-input name="content" label="conteúdo ministrado">
                <x-slot name="input">

                    <textarea 
                        @click="errors.register.hasError = false"
                        x-model="data.register" 
                        class="block w-full border-red-500 form-textarea" 
                        :class="{'border-red-500' : errors.register.hasError}" 
                        id="content" 
                        name="content" 
                        rows="4" 
                        placeholder="Digite aqui o conteúdo ministrado nesta aula"
                    >
                    </textarea>

                    <span class="block text-sm text-red-500" x-show="errors.register.hasError" x-text="errors.register.message"></span>

                </x-slot>
            </x-card.form-input>

        </x-slot>

        <x-slot name="footer">
            <div x-show="message.show" class="block mr-auto"><span x-text="message.content"></span></div>
            <button 
                @click.prevent="saveDraft()" 
                class="px-4 py-2 text-sm font-medium leading-none text-teal-100 capitalize bg-teal-500 hover:bg-teal-600 hover:text-white rounded-md shadown"
            >
                salvar
            </button>
            <button 
                @click.prevent="register()" 
                class="px-4 py-2 text-sm font-medium leading-none text-teal-100 capitalize bg-teal-500 hover:bg-teal-600 hover:text-white rounded-md shadown"
            >
                registrar
            </button>
        </x-slot>

    </x-card.form-layout>

@endsection

@section('scripts')

    @parent

    <script>
        function form() {
            return {
                data: {
                    register: '{{ $lesson->register }}',
                    presenceList: {!! $lesson->novicesFrequencyToJsonObject() !!},
                },
                message: {
                    content: '',
                    show: false,
                },
                errors: {
                    register: {
                        hasError: false,
                        message: 'The field is required',
                    },
                },
                register() {
                    axios.post('lessons/register/{{ $lesson->id }}', this.data)
                        .then(response => {this.redirectToLesson()})
                        .catch(error => {this.handleErrors(error.response.data.errors)});
                },
                changeFrequency(novice) {
                    key = Object.keys(novice)[0];
                    this.data.presenceList[key] = novice[key];
                },
                saveDraft() {
                    axios.post('lessons/draft/{{ $lesson->id }}', this.data)
                        .then(response => {this.showSuccessMessage('Rascunho salvo com sucesso!')})
                        .catch(error => {this.handleErrors(error.response.data.errors)});
                },
                redirectToLesson() {
                    window.location.assign('{{ route('lessons.show', ['lesson' => $lesson->id]) }}');
                },
                showSuccessMessage($message) {
                    this.message.content = $message;
                    this.message.show = true;
                },
                handleErrors(errors) {
                    Object.entries(errors).forEach((key) => {
                        if (key[0] == 'register') {
                            this.showRegisterError(key[1]);
                        }
                    });
                },
                showRegisterError(message) {
                    this.errors.register.hasError = true;
                    this.errors.register.message = message;
                },
            } 
        }
    </script>
@endsection
