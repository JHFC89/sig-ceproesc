@extends('layouts.dashboard')

@section('title', 'Registro De Aula')

@section('content')

    @if (session()->has('status'))
    <x-alert 
        type="success" 
        message="{{ session('status') }}" 
    />
    @endif

    @error('grade')
    <x-alert 
        type="warning" 
        :message="$message" 
    />
    @enderror

    <x-card.list.description-layout title="atividade avaliativa">
        <x-slot name="items">
            <x-card.list.description-item label="aula" type="link" :href="route('lessons.show', ['lesson' => $evaluation->lesson])" :description="$evaluation->lesson->formatted_date"/>
            <x-card.list.description-item label="nome" :description="$evaluation->label"/>
            <x-card.list.description-item label="descrição" type="text" :description="$evaluation->description"/>
        </x-slot>
    </x-card.list.description-layout>

    @unless(Auth::user()->isNovice())
    <x-card.list.table-layout title="indicadores de avaliação">

        <x-slot name="header">
            <x-card.list.table-header class="col-span-1" name="código"/>
            @can('createGrade', $evaluation)
                <x-card.list.table-header class="col-span-5" name="nome"/>
                <x-card.list.table-header class="col-span-2" name="turma"/>
                <x-card.list.table-header class="text-center col-span-3" name="registrar indicador"/>
            @else
                <x-card.list.table-header class="col-span-6" name="nome"/>
                <x-card.list.table-header class="col-span-2" name="turma"/>
            @endcan
        </x-slot>

        <x-slot name="body">

            @foreach($evaluation->lesson->novices as $novice)
                @can('viewNoviceEvaluation', [App\Models\Evaluation::class, $novice])
                <x-card.list.table-row>
                    <x-slot name="items">

                        <x-card.list.table-body-item class="col-span-1">
                            <x-slot name="item">
                                <span class="flex items-center h-full">{{ $novice->code }}</span>
                            </x-slot>
                        </x-card.list.table-body-item>

                        @can('createGrade', $evaluation)

                            <x-card.list.table-body-item class="col-span-5">
                                <x-slot name="item">
                                    <span class="flex items-center h-full">{{ $novice->name }}</span>
                                </x-slot>
                            </x-card.list.table-body-item>

                            <x-card.list.table-body-item class="col-span-2">
                                <x-slot name="item">
                                    <span class="flex items-center h-full">{{ $novice->class }}</span>
                                </x-slot>
                            </x-card.list.table-body-item>

                            <x-card.list.table-body-item class="col-span-3">
                                <x-slot name="item">
                                    <div class="flex justify-center">
                                        <select class="form-select" name="gradesList[{{ $novice->id }}]" form="gradesListForm" {{ $evaluation->lesson->isAbsent($novice) ? 'disabled' : '' }}>
                                            <option value="a">A</option>
                                            <option value="b">B</option>
                                            <option value="c">C</option>
                                            <option value="d">D</option>
                                        </select>
                                    </div>
                                </x-slot>
                            </x-card.list.table-body-item>

                        @else

                            <x-card.list.table-body-item class="col-span-6">
                                <x-slot name="item">
                                    <span class="flex items-center h-full">{{ $novice->name }}</span>
                                </x-slot>
                            </x-card.list.table-body-item>

                            <x-card.list.table-body-item class="col-span-2">
                                <x-slot name="item">
                                    <span class="flex items-center h-full">{{ $novice->class }}</span>
                                </x-slot>
                            </x-card.list.table-body-item>

                        @endcan

                    </x-slot>
                </x-card.list.table-row>
                @endcan
            @endforeach

        </x-slot>

    </x-card.list.table-layout>
    @endunless

    @can('createGrade', $evaluation)
    <div class="flex justify-end">
        <form action="{{ route('evaluations.grades.store', ['evaluation' => $evaluation]) }}" method="POST" id="gradesListForm">
            @csrf
            <button class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown">registrar notas</button>
        </form>
    </div>
    @endcan

@endsection
