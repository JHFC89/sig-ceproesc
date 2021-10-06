@props(['title', 'overflowHidden' => true])

@php
$classes = 'mt-4 capitalize bg-white shadow rounded-md';

if ($overflowHidden) {
$classes = $classes . ' overflow-hidden';
}

@endphp

<div>
    <h2 class="text-xl font-medium text-gray-700 capitalize">{{ $title }}</h2>
    <div {{ $attributes->merge(['class' => $classes]) }}>
        {{ $content }}
    </div>
</div>
