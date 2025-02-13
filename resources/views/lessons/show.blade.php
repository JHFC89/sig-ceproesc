@extends('layouts.dashboard')

@section('title', 'Aula')

@section('content')

    @can('createForLesson', [App\Models\LessonRequest::class, $lesson])
        @if($lesson->isExpired())
        <x-alert 
            type="warning" 
            message="Prazo para registro dessa aula vencido." 
            actionText="Solicitar liberação da aula." 
            :actionLink="route('lessons.requests.create', ['lesson' => $lesson])"
        />
        @endif
    @elsecan('view', $lesson->requests->first())
        @if($lesson->hasPendingRequest())
        <x-alert 
            type="success" 
            :message="$lesson->isRegistered() ? 'Aula liberada para retificação.' : 'Aula vencida liberada para registro.'" 
            actionText="{{ Auth::user()->can('createRegister', $lesson) ? 'Registrar' : '' }}" 
            :actionLink="Auth::user()->can('createRegister', $lesson) ? route('lessons.registers.create', ['lesson' => $lesson]) : ''"
        />
        @elseif($lesson->hasOpenRequest())
        <x-alert 
            type="attention" 
            :message="$lesson->openRequest()->isRectification() ? 'Aula com pedido de retificação em aberto.' : 'Aula com pedido de liberação para registro em aberto.'" 
            actionText="Ver solicitação." 
            :actionLink="route('requests.show', ['request' => $lesson->openRequest()])"
        />
        @endif
    @endcan

    <x-card.list.description-layout title="detalhes da aula">
        <x-slot name="items">
            @if(Auth::user()->isNovice())
            <x-card.list.description-item label="aprendiz" :description="$lesson->novices->find(Auth::user())->name"/>
            @endif
            <x-card.list.description-item label="instrutor" :description="$lesson->instructor->name"/>
            <x-card.list.description-item label="data" :description="$lesson->formatted_date"/>
            <x-card.list.description-item label="horário" :description="$lesson->formatted_type"/>
            @if(Auth::user()->isNovice())
            <x-card.list.description-item label="turma" :description="Auth::user()->class"/>
            @else
            <x-card.list.description-item label="turma" :description="$lesson->formatted_course_classes"/>
            @endif
            <x-card.list.description-item label="disciplina" :description="$lesson->discipline->name"/>
            <x-card.list.description-item label="carga horária" :description="$lesson->hourly_load"/>
            @if(Auth::user()->isNovice() && $lesson->isRegistered())
            <x-card.list.description-item label="presença" :description="Auth::user()->presentForLesson($lesson) ? 'presente' : 'ausente'"/>
            <x-card.list.description-item type="text" label="observação" :description="Auth::user()->observationForLesson($lesson) ?: 'Nenhuma obervação registrada'"/>
            @endif
            @if($lesson->isRegistered())
            <x-card.list.description-item type="text" label="registro" :linebreak="true" :description="$lesson->register"/>
            @endif
            @if(Auth::user()->can('createForLesson', [App\Models\Evaluation::class, $lesson]))
            <x-card.list.description-item type="link" :href="route('lessons.evaluations.create', ['lesson' => $lesson])" label="atividade avaliativa" description="criar"/>
            @elseif($lesson->hasEvaluation())    
            <x-card.list.description-item type="link" :href="route('evaluations.show', ['evaluation' => $lesson->evaluation])" label="atividade avaliativa" :description="$lesson->evaluation->label"/>
            @endif
        </x-slot>
    </x-card.list.description-layout>

    @unless(Auth::user()->isNovice())
    <x-card.list.table-layout title="lista de presença">

        <x-slot name="header">
            <x-card.list.table-header class="col-span-1" name="código"/>
            <x-card.list.table-header class="{{ $lesson->isRegistered() ? 'col-span-3' : 'col-span-6'}}" name="nome"/>
            <x-card.list.table-header class="col-span-2" name="turma"/>
            @if($lesson->isRegistered())
            <x-card.list.table-header class="text-center col-span-2" name="presença"/>
            <x-card.list.table-header class="col-span-4" name="observação"/>
            @endif
        </x-slot>

        <x-slot name="body">

            @foreach($lesson->novices as $novice)
                @can('viewLessonNovice', [App\Models\Lesson::class, $novice])
                <x-card.list.table-row>
                    <x-slot name="items">

                        <x-card.list.table-body-item class="col-span-1">
                            <x-slot name="item">
                                <span>{{ $novice->code }}</span>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="{{ $lesson->isRegistered() ? 'col-span-3' : 'col-span-6'}}">
                            <x-slot name="item">
                                <span>{{ $novice->name }}</span>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="col-span-2">
                            <x-slot name="item">
                                <span>{{ $novice->class }}</span>
                            </x-slot>
                        </x-card.list.table-body-item>

                        @if($lesson->isRegistered())
                        <x-card.list.table-body-item class="col-span-2">
                            <x-slot name="item">
                                <div class="flex items-center justify-center h-full">
                                    <x-icons.active class="w-2 h-2" :active="$novice->presentForLesson($lesson)"/>
                                </div>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="col-span-4">
                            <x-slot name="item">
                                <span class="normal-case">{!! $novice->observationForLesson($lesson) ?: '<span class="text-gray-300">Nenhuma observação registrada</span>' !!}</span>
                            </x-slot>
                        </x-card.list.table-body-item>
                        @endif

                    </x-slot>
                </x-card.list.table-row>
                @endcan
            @endforeach

        </x-slot>

    </x-card.list.table-layout>
    @endunless

    @can('createRegister', $lesson)
    <div class="flex justify-end">
        <a href="{{ route('lessons.registers.create', ['lesson' => $lesson]) }}" class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown">registrar</a>
    </div>
    @elsecan('createForLesson', [App\Models\LessonRequest::class, $lesson])
    <div class="flex justify-end">
        <a href="{{ route('lessons.requests.create', ['lesson' => $lesson]) }}" class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown">retificar registro</a>
    </div>
    @endcan

    @can('update', $lesson)
    <div class="flex justify-end">
        <a href="{{ route('lessons.edit', ['lesson' => $lesson]) }}" class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown">alterar</a>
    </div>
    @endcan

@endsection
