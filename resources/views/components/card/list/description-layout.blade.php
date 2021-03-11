@props(['title'])

<x-card.panel-layout :title="$title" class="px-6 py-2 divide-y">
    <x-slot name="content">
        {{ $items }}
    </x-slot>
</x-card-panel-layout>
