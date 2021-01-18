@extends('layouts.dashboard')

@section('title', 'Registro De Aula')

@section('content')

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
            <x-card.list.table-header class="col-span-6" name="nome"/>
            <x-card.list.table-header class="col-span-2" name="turma"/>
        </x-slot>

        <x-slot name="body">

            @foreach($evaluation->lesson->novices as $novice)
                @can('viewNoviceEvaluation', [App\Models\Evaluation::class, $novice])
                <x-card.list.table-row>
                    <x-slot name="items">

                        <x-card.list.table-body-item class="col-span-1">
                            <x-slot name="item">
                                <span>{{ $novice->code }}</span>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="col-span-6">
                            <x-slot name="item">
                                <span>{{ $novice->name }}</span>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="col-span-2">
                            <x-slot name="item">
                                <span>{{ $novice->class }}</span>
                            </x-slot>
                        </x-card.list.table-body-item>

                    </x-slot>
                </x-card.list.table-row>
                @endcan
            @endforeach

        </x-slot>

    </x-card.list.table-layout>
    @endunless

@endsection
