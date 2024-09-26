@extends('layouts.dashboard')

@prepend('head')
<script defer src="https://unpkg.com/alpinejs@3.2.3/dist/cdn.min.js"></script>
@endprepend

@section('title', 'Ficha Cadastral')

@section('content')

@if (session()->has('status'))
<x-alert type="success" message="{{ session('status') }}" />
@endif

<x-card.list.table-layout title="fichas dos candidatos" :overflow-hidden="false">

    <x-slot name="beforeHeader">

        <div class="flex justify-between p-3 border-b">

            <div></div>

            <div x-data="filter" @page.window="send($event.detail)" class="relative">

                <button @click="show = !show" type="button" class="flex items-center p-2 bg-gray-100 rounded-md">
                    <x-icons.filter class="h-5 w-5 text-gray-500" />
                    <x-icons.chevron-down class="h-5 w-5 fill-current" />
                </button>

                <div x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90" style="display: none !important" class="absolute -mr-8 mt-10 top-0 right-0 min-w-full w-64 z-30">
                    <span class="absolute top-0 right-0 mr-12 -mt-2 w-4 h-4 bg-gray-100 transform rotate-45 border-l border-t z-20"></span>
                    <div class="relative top-0 right-0 w-full overflow-hidden bg-white border rounded-md shadow-md">
                        <ul class="font-bold font-mono uppercase text-sm tracking-wide">

                            <li>
                                <label for="name" class="block px-2 py-1 bg-gray-100">nome</label>
                                <div class="px-2 py-2">
                                    <input x-model="filter.name.value" name="name" id="name" type="text" class="block w-full form-input font-sans py-0 px-1 rounded-md">
                                </div>
                            </li>

                            <li>
                                <label for="age" class="block px-2 py-1 bg-gray-100">idade</label>
                                <div class="px-2 py-2 flex items-center gap-x-2">
                                    <input x-model="filter.agefrom.value" name="agefrom" id="agefrom" type="text" class="w-full form-input font-sans py-0 px-1 rounded-md">
                                    <span class="font-sans font-normal normal-case">até</span>
                                    <input x-model="filter.ageto.value" name="ageto" id="ageto" type="text" class="w-full form-input font-sans py-0 px-1 rounded-md">
                                </div>
                            </li>

                            <li>
                                <label for="gender" class="block px-2 py-1 bg-gray-100">gênero</label>
                                <div class="px-2 py-2">
                                    <select x-model="filter.gender.value" name="gender" id="gender" type="select" class="block w-full form-select font-sans py-0 px-1 rounded-md">
                                        <option value="">---</option>
                                        <option value="masculino">Masculino</option>
                                        <option value="feminino">Feminino</option>
                                    </select>
                                </div>
                            </li>

                            <li>
                                <label for="schooling" class="block px-2 py-1 bg-gray-100">escolaridade</label>
                                <div class="px-2 py-2">
                                    <select x-model="filter.schooling.value" name="schooling" id="schooling" type="select" class="block w-full form-select font-sans py-0 px-1 rounded-md">
                                        <option value="">---</option>
                                        <option value="ensino fundamental">Ensino Fundamental</option>
                                        <option value="ensino médio">Ensino Médio</option>
                                        <option value="ensino técnico">Ensino Técnico</option>
                                        <option value="ensino superior">Ensino Superior</option>
                                    </select>
                                </div>
                            </li>

                            <li>
                                <label for="course" class="block px-2 py-1 bg-gray-100">curso</label>
                                <div class="px-2 py-2">
                                    <input x-model="filter.course.value" name="course" id="course" type="text" class="block w-full form-input font-sans py-0 px-1 rounded-md">
                                </div>
                            </li>

                            <li>
                                <label for="complementary" class="block px-2 py-1 bg-gray-100">cursos complementares</label>
                                <div class="px-2 py-2">
                                    <input x-model="filter.complementary.value" name="complementary" id="complementary" type="text" class="block w-full form-input font-sans py-0 px-1 rounded-md">
                                </div>
                            </li>

                            <li>
                                <label for="district" class="block px-2 py-1 bg-gray-100">bairro</label>
                                <div class="px-2 py-2">
                                    <input x-model="filter.district.value" name="district" id="district" type="text" class="block w-full form-input font-sans py-0 px-1 rounded-md">
                                </div>
                            </li>

                            <li>
                                <label for="city" class="flex flex-col justify-center px-2 py-1 bg-gray-100">
                                    cidade
                                    <span class="text-xs font-sans font-normal normal-case">(Separado por vírgulas e sem espaço)</span>
                                </label>
                                <div class="px-2 py-2">
                                    <input x-model="filter.city.value" name="city" id="city" type="text" placeholder="Araraquara,São Carlos,Matão" class="block w-full form-input font-sans py-0 px-1 rounded-md">
                                </div>
                            </li>

                            <li>
                                <label for="employed" class="block px-2 py-1 bg-gray-100">situação profissional</label>
                                <div class="px-2 py-2">
                                    <select x-model="filter.employed.value" name="employed" id="employed" type="select" class="block w-full form-select font-sans py-0 px-1 rounded-md">
                                        <option value="">---</option>
                                        <option value="sim">Empregado</option>
                                        <option value="não">Desempregado</option>
                                    </select>
                                </div>
                            </li>

                            <li>
                                <label for="job" class="block px-2 py-1 bg-gray-100">Vaga de interesse</label>
                                <div class="px-2 py-2">
                                    <input x-model="filter.job.value" name="job" id="job" type="text" class="block w-full form-input font-sans py-0 px-1 rounded-md">
                                </div>
                            </li>

                            <li>
                                <label for="program" class="block px-2 py-1 bg-gray-100">Programa de interesse</label>
                                <div class="px-2 py-2">
                                    <select x-model="filter.program.value" name="program" id="program" type="select" class="block w-full form-select font-sans py-0 px-1 rounded-md">
                                        <option value="">---</option>
                                        <option value="aprendiz">Aprendiz</option>
                                        <option value="estágio">Estágio</option>
                                    </select>
                                </div>
                            </li>

                            <li class="flex justify-end px-2 py-2">
                                <button @click.prevent="send(false)" class="px-4 py-1 text-white font-sans uppercase text-xs bg-blue-500 rounded-md shadow-md hover:bg-blue-700">filtrar</button>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

    </x-slot>

    <x-slot name="header">
        <x-card.list.table-header class="col-span-1" name="data" />
        <x-card.list.table-header class="col-span-3" name="nome" />
        <x-card.list.table-header class="col-span-1 text-center" name="idade" />
        <x-card.list.table-header class="col-span-1" name="gênero" />
        <x-card.list.table-header class="col-span-2" name="cidade" />
        <x-card.list.table-header class="col-span-2" name="escolaridade" />
        <x-card.list.table-header class="col-span-1" name="programa" />
        <x-card.list.table-header class="col-span-1" name="" />
    </x-slot>

    <x-slot name="body">

        @foreach ($entries as $entry)
        <x-card.list.table-row>
            <x-slot name="items">

                <x-card.list.table-body-item class="flex items-center col-span-1">
                    <x-slot name="item">
                        <div class="flex items-center h-full w-full">
                            <span class="normal-case">{{ $entry->created_at->format('d-m-Y') }}</span>
                        </div>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="flex items-center col-span-3">
                    <x-slot name="item">
                        <span>{{ $entry->nome }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="flex items-center col-span-1">
                    <x-slot name="item">
                        <div class="flex items-center justify-center h-full w-full">
                            <span class="normal-case">{{ \Carbon\Carbon::parse($entry->data_de_nascimento)->diff(\Carbon\Carbon::now())->format('%y') }}</span>
                        </div>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="flex items-center col-span-1">
                    <x-slot name="item">
                        <span>{{ $entry->genero }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="flex items-center col-span-2">
                    <x-slot name="item">
                        <span class="normal-case">{{ $entry->cidade_onde_mora }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="flex items-center col-span-2">
                    <x-slot name="item">
                        <span>{{ $entry->escolaridade }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="flex items-center col-span-1">
                    <x-slot name="item">
                        <span>{{ $entry->programa }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="flex items-center col-span-1">
                    <x-slot name="item">
                        <div class="flex justify-end space-x-2 w-full">
                            @if ($entry->vaga)
                                <span title="{{ $entry->vaga }}">
                                    <svg class="w-6 text-gray-300 hover:text-blue-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M12.3 7.29c.2-.18.44-.29.7-.29c.27 0 .5.11.71.29c.19.21.29.45.29.71c0 .27-.1.5-.29.71c-.21.19-.44.29-.71.29c-.26 0-.5-.1-.7-.29c-.19-.21-.3-.44-.3-.71c0-.26.11-.5.3-.71m-2.5 4.68s2.17-1.72 2.96-1.79c.74-.06.59.79.52 1.23l-.01.06c-.14.53-.31 1.17-.48 1.78c-.38 1.39-.75 2.75-.66 3c.1.34.72-.09 1.17-.39c.06-.04.11-.08.16-.11c0 0 .08-.08.16.03c.02.03.04.06.06.08c.09.14.14.19.02.27l-.04.02c-.22.15-1.16.81-1.54 1.05c-.41.27-1.98 1.17-1.74-.58c.21-1.23.49-2.29.71-3.12c.41-1.5.59-2.18-.33-1.59c-.37.22-.59.36-.72.45c-.11.08-.12.08-.19-.05l-.03-.06l-.05-.08c-.07-.1-.07-.11.03-.2M22 12c0 5.5-4.5 10-10 10S2 17.5 2 12S6.5 2 12 2s10 4.5 10 10m-2 0c0-4.42-3.58-8-8-8s-8 3.58-8 8s3.58 8 8 8s8-3.58 8-8"/></svg>
                                </span>
                            @endif
                            <a href="{{ route('candidates.show', ['entry' => $entry ]) }}" class="text-gray-300 hover:text-blue-300">
                                <x-icons.see class="w-6" />
                            </a>
                        </div>
                    </x-slot>
                </x-card.list.table-body-item>

            </x-slot>
        </x-card.list.table-row>
        @endforeach


    </x-slot>

</x-card.list.table-layout>

<div x-data @click.prevent="$dispatch('page',$event.target.tagName == 'svg' ? $event.target.parentElement.href.split('?')[1] : $event.target.href.split('?')[1])" class="mt-8 normal-case">
    {{ $entries->links() }}
</div>

@endsection

@prepend('footer')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('filter', () => ({
            filter: {
                name: {
                    value: '{{ request()->input('filter.name', null) }}',
                    active: {{ request()->has('filter.name') ? 'true' : 'false' }}
                },
                agefrom: {
                    value: '{{ request()->input('filter.agefrom', null) }}',
                    active: {{ request()->has('filter.agefrom') ? 'true' : 'false' }}
                },
                ageto: {
                    value: '{{ request()->input('filter.ageto', null) }}',
                    active: {{ request()->has('filter.ageto') ? 'true' : 'false' }}
                },
                gender: {
                    value: '{{ request()->input('filter.gender', null) }}',
                    active: {{ request()->has('filter.gender') ? 'true' : 'false' }}
                },
                schooling: {
                    value: '{{ request()->input('filter.schooling', null) }}',
                    active: {{ request()->has('filter.schooling') ? 'true' : 'false' }}
                },
                course: {
                    value: '{{ request()->input('filter.course', null) }}',
                    active: {{ request()->has('filter.course') ? 'true' : 'false' }}
                },
                complementary: {
                    value: '{{ request()->input('filter.complementary', null) }}',
                    active: {{ request()->has('filter.complementary') ? 'true' : 'false' }}
                },
                district: {
                    value: '{{ request()->input('filter.district', null) }}',
                    active: {{ request()->has('filter.district') ? 'true' : 'false' }}
                },
                employed: {
                    value: '{{ request()->input('filter.employed', null) }}',
                    active: {{ request()->has('filter.employed') ? 'true' : 'false' }}
                },
                city: {
                    value: '{{ request()->input('filter.city', null) }}',
                    active: {{ request()->has('filter.city') ? 'true' : 'false' }}
                },
                job: {
                    value: '{{ request()->input('filter.job', null) }}',
                    active: {{ request()->has('filter.job') ? 'true' : 'false' }}
                },
                program: {
                    value: '{{ request()->input('filter.program', null) }}',
                    active: {{ request()->has('filter.program') ? 'true' : 'false' }}
                }
            },

            show: {{ request()->boolean('show') ? 'true' : 'false' }},

            init() {
                Object.keys(this.filter).forEach((key) => {
                    this.$watch(`filter.${key}.value`, value => this.enableField(key, value));
                })
            },

            enableField(field, value) {
                this.filter[field]['active'] = !(value == null || value == '')
            },

            prepareFilters(filters) {
                return Object.entries(filters).reduce(function(mapped, filter) {

                    if (!filter[1].active || filter[1].value == null) {
                        return mapped;
                    }

                    item = [];

                    item[0] = filter[0];
                    item[1] = filter[1].value;

                    mapped.push(item);

                    return mapped;
                }, [])
            },

            parseQuery(filters) {
                query = filters.reduce(function(query, filter, i, a) {
                    query = query + `filter[${filter[0]}]=${filter[1]}`;

                    if ((i + 1) < a.length) {
                        query = query + '&';
                    }

                    return query

                }, '?')

                return `${query}&show=${this.show}`
            },

            getQuery() {
                filters = this.prepareFilters(this.filter)

                return this.parseQuery(filters)
            },

            send(page = false) {
                query = this.getQuery()

                if (page !== false) {
                    query = `${query}&${page}`
                }

                window.location.href = window.location.href.split('?')[0] + query
            }
        }))
    })
</script>
@endprepend
