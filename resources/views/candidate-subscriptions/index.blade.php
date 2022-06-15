@extends('layouts.dashboard')

@prepend('head')
<script defer src="https://unpkg.com/alpinejs@3.2.3/dist/cdn.min.js"></script>
@endprepend

@section('title', 'Ficha Cadastral')

@section('content')

@if (session()->has('status'))
    <x-alert type="success" message="{{ session('status') }}"/>
@endif

<x-card.list.table-layout title="fichas dos candidatos" :overflow-hidden="false">

    <x-slot name="beforeHeader">

        <div class="flex justify-between p-3 border-b">

            <div></div>

            <div x-data="filter" @page.window="send($event.detail)" class="relative">

                <button @click="show = !show" type="button" class="flex items-center p-2 bg-gray-100 rounded-md">
                    <x-icons.filter class="h-5 w-5 text-gray-500"/>
                    <x-icons.chevron-down class="h-5 w-5 fill-current"/>
                </button>

                <div
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    style="display: none !important"
                    class="absolute -mr-8 mt-10 top-0 right-0 min-w-full w-64 z-30"
                >
                    <span class="absolute top-0 right-0 mr-12 -mt-2 w-4 h-4 bg-gray-100 transform rotate-45 border-l border-t z-20"></span>
                    <div class="relative top-0 right-0 w-full overflow-hidden bg-white border rounded-md shadow-md">
                        <ul class="font-bold font-mono uppercase text-sm tracking-wide">

                            <li>
                                <label for="name" class="block px-2 py-1 bg-gray-100">nome</label>
                                <div class="px-2 py-2">
                                    <input
                                        x-model="filter.name.value"
                                        name="name"
                                        id="name"
                                        type="text"
                                        class="block w-full form-input font-sans py-0 px-1 rounded-md"
                                    >
                                </div>
                            </li>

                            <li>
                                <label for="age" class="block px-2 py-1 bg-gray-100">idade</label>
                                <div class="px-2 py-2">
                                    <input
                                        x-model="filter.age.value"
                                        name="age"
                                        id="age"
                                        type="text"
                                        class="block w-full form-input font-sans py-0 px-1 rounded-md"
                                    >
                                </div>
                            </li>

                            <li>
                                <label for="gender" class="block px-2 py-1 bg-gray-100">gênero</label>
                                <div class="px-2 py-2">
                                    <select
                                        x-model="filter.gender.value"
                                        name="gender"
                                        id="gender"
                                        type="select"
                                        class="block w-full form-select font-sans py-0 px-1 rounded-md"
                                    >
                                        <option value="">---</option>
                                        <option value="masculino">Masculino</option>
                                        <option value="feminino">Feminino</option>
                                    </select>
                                </div>
                            </li>

                            <li>
                                <label for="schooling" class="block px-2 py-1 bg-gray-100">escolaridade</label>
                                <div class="px-2 py-2">
                                    <select
                                        x-model="filter.schooling.value"
                                        name="schooling"
                                        id="schooling"
                                        type="select"
                                        class="block w-full form-select font-sans py-0 px-1 rounded-md"
                                    >
                                        <option value="">---</option>
                                        <option value="ensino fundamental">Ensino Fundamental</option>
                                        <option value="ensino médio">Ensino Médio</option>
                                        <option value="ensino superior">Ensino Superior</option>
                                    </select>
                                </div>
                            </li>

                            <li>
                                <label for="course" class="block px-2 py-1 bg-gray-100">curso</label>
                                <div class="px-2 py-2">
                                    <input
                                        x-model="filter.course.value"
                                        name="course"
                                        id="course"
                                        type="text"
                                        class="block w-full form-input font-sans py-0 px-1 rounded-md"
                                    >
                                </div>
                            </li>

                            <li>
                                <label for="complementary" class="block px-2 py-1 bg-gray-100">cursos complementares</label>
                                <div class="px-2 py-2">
                                    <input
                                        x-model="filter.complementary.value"
                                        name="complementary"
                                        id="complementary"
                                        type="text"
                                        class="block w-full form-input font-sans py-0 px-1 rounded-md"
                                    >
                                </div>
                            </li>

                            <li>
                                <label for="district" class="block px-2 py-1 bg-gray-100">bairro</label>
                                <div class="px-2 py-2">
                                    <input
                                        x-model="filter.district.value"
                                        name="district"
                                        id="district"
                                        type="text"
                                        class="block w-full form-input font-sans py-0 px-1 rounded-md"
                                    >
                                </div>
                            </li>

                            <li>
                                <label for="city" class="block px-2 py-1 bg-gray-100">cidade</label>
                                <div class="px-2 py-2">
                                    <input
                                        x-model="filter.city.value"
                                        name="city"
                                        id="city"
                                        type="text"
                                        class="block w-full form-input font-sans py-0 px-1 rounded-md"
                                    >
                                </div>
                            </li>

                            <li>
                                <label for="employed" class="block px-2 py-1 bg-gray-100">situação profissional</label>
                                <div class="px-2 py-2">
                                    <select
                                        x-model="filter.employed.value"
                                        name="employed"
                                        id="employed"
                                        type="select"
                                        class="block w-full form-select font-sans py-0 px-1 rounded-md"
                                    >
                                        <option value="">---</option>
                                        <option value="sim">Empregado</option>
                                        <option value="não">Desempregado</option>
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
        <x-card.list.table-header class="col-span-3" name="nome"/>
        <x-card.list.table-header class="col-span-2 text-center" name="idade"/>
        <x-card.list.table-header class="col-span-2" name="gênero"/>
        <x-card.list.table-header class="col-span-2" name="cidade"/>
        <x-card.list.table-header class="col-span-2" name="escolaridade"/>
        <x-card.list.table-header class="col-span-1" name=""/>
    </x-slot>

    <x-slot name="body">

        @foreach ($entries as $entry)
            <x-card.list.table-row>
                <x-slot name="items">

                    <x-card.list.table-body-item class="flex items-center col-span-3">
                        <x-slot name="item">
                            <span>{{ $entry->answers->for('q1')->value }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-2">
                        <x-slot name="item">
                            <div class="flex items-center justify-center h-full w-full">
                                <span class="normal-case">{{ \Carbon\Carbon::parse($entry->answers->for('q3')->value)->diff(\Carbon\Carbon::now())->format('%y') }}</span>
                            </div>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-2">
                        <x-slot name="item">
                            <span>{{ $entry->answers->for('q4')->value }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-2">
                        <x-slot name="item">
                            <span class="normal-case">{{ $entry->answers->for('q8')->value }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-2">
                        <x-slot name="item">
                            <span>{{ $entry->answers->for('q37')->value }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-1">
                        <x-slot name="item">
                            <div class="flex justify-end space-x-2 w-full">
                                <a 
                                    href="{{ route('candidate-subscriptions.show', ['entry' => $entry ]) }}"
                                    class="text-gray-300 hover:text-blue-300"
                                >
                                    <x-icons.see class="w-6"/>
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
                age: {
                    value: '{{ request()->input('filter.age', null) }}',
                    active: {{ request()->has('filter.age') ? 'true' : 'false' }} 
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
                return Object.entries(filters).reduce(function (mapped, filter) {

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
                query = filters.reduce(function (query, filter, i, a) {
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
