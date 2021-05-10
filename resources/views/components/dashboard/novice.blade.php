@if ($show)

    @if ($frequency)
    <div class="flex w-full">
        <x-card.panel-layout title="frequência" class="px-6 py-2">
            <x-slot name="content">
                <div class="font-semibold text-lg">{{ $frequency }} %</div>
            </x-slot>
        </x-card.panel-layout>
    </div>
    @endif

    <div class="w-full">
        <x-lesson.for-today-list title="aulas de hoje" :hideRegistered="true" :alwaysShow="false" :user="request()->user()"/>
    </div>

    <div class="w-full">
        <x-lesson.for-week-list title="aulas da semana" :hideRegistered="true" :user="request()->user()"/>
    </div>

@endif
