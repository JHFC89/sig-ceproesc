@props(['text', 'color'])

@php
    switch ($color) {
        case 'red':
            $colorClasses = 'bg-pink-100 text-red-700';
            break;
        case 'blue':
            $colorClasses = 'bg-blue-100 text-blue-700';
            break;
    }
@endphp

<span class="flex items-center justify-center rounded-full tracking-wide text-xs font-bold px-3 py-1 {{ $colorClasses }}">{{ $text }}</span>
