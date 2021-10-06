@props(['title','header', 'body', 'overflowHidden' => true])

<div {{ $attributes }}>

    <x-card.panel-layout :title="$title" :overflow-hidden="$overflowHidden">
        <x-slot name="content">

            {{ $beforeHeader ?? null }}

            <div class="capitalize bg-gray-100 border-b">
                <div class="px-6 py-2 font-mono text-sm font-bold tracking-wide text-gray-600 uppercase grid-cols-12 grid">
                {{ $header }}
                </div>
            </div>

            <div class="capitalize bg-white divide-y">
                {{ $body }}
            </div>

        </x-slot>
    </x-card-panel-layout>

</div>
