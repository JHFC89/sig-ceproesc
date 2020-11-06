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

            <x-card.list.table-body-item class="col-span-1">
                <x-slot name="item">
                    <span>123</span>
                </x-slot>
            </x-card.list.table-body-item>

            <x-card.list.table-body-item class="col-span-5">
                <x-slot name="item">
                    <span>{{ $lesson->novice }}</span>
                </x-slot>
            </x-card.list.table-body-item>

            <x-card.list.table-body-item class="col-span-1">
                <x-slot name="item">
                    <span>2021 - janeiro</span>
                </x-slot>
            </x-card.list.table-body-item>

            <x-card.list.table-body-item class="col-span-5">
                <x-slot name="item">
                    <div class="space-x-4">
                        <label class="inline-flex items-center space-x-2">
                            <input class="form-radio" type="radio" name="presence1" value="0">
                            <span>0</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input class="form-radio" type="radio" name="presence1" value="1">
                            <span>1</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input class="form-radio" type="radio" name="presence1" value="2">
                            <span>2</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input class="form-radio" type="radio" name="presence1" value="3" checked>
                            <span>3</span>
                        </label>
                    </div>
                </x-slot>
            </x-card.list.table-body-item>

        </x-slot>
    </x-card.list.table-layout>

    <x-card.form-layout x-data="form()" title="registro da aula">

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
