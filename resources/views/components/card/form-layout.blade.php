@props(['title', 'inputs', 'action' => '', 'method' => 'POST', 'single' => true])

@if ($single)
    <x-card.panel-layout :title="$title" {{ $attributes }}>
        <x-slot name="content">

            <form 
                action="{{ $action }}"
                method="POST"
            >
                @csrf

                @if ($method == 'PATCH' || $method == 'PUT')
                    @method('PATCH')
                @endif

                <div class="px-6 py-2 divide-y">
                    {{ $inputs }}
                </div>

                <div class="flex items-center justify-end px-6 py-4 bg-gray-100 space-x-2">
                    {{ $footer }}
                </div>

            </form>

        </x-slot>
    </x-card.panel-layout>

@else
    <div {{ $attributes }}>

        <form 
            class="space-y-8 space-y-reverse"
            action="{{ $action }}"
            method="POST"
        >
            @csrf

            @if ($method == 'PATCH' || $method == 'PUT')
                @method('PATCH')
            @endif

            {{ $panels }}

            <div class="flex items-center justify-end space-x-2">
                {{ $footer }}
            </div>

        </form>

    </div>
@endif
