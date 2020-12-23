@props(['type', 'message', 'actionText' => false, 'actionLink' => false])

@php
    switch ($type) {
        case 'success':
            $colorClasses = 'bg-green-500 text-green-100';
            $actionClasses = 'hover:text-green-300';
            break;
        case 'warning':
            $colorClasses = 'bg-red-500 text-red-100';
            $actionClasses = 'hover:text-red-300';
            break;
        case 'attention':
            $colorClasses = 'bg-yellow-200 text-yellow-700';
            $actionClasses = 'hover:text-yellow-500';
            break;
    }
@endphp

<div class="px-4 py-6 w-full rounded-md font-medium flex items-center {{ $colorClasses }}">
    @if($type == 'success')
    <span><x-icons.check class="w-6 h-6"/></span>
    @elseif($type == 'warning')
    <span><x-icons.error class="w-6 h-6"/></span>
    @endif
    <span class="ml-2">{{ $message }}</span>
    @if($actionText)
    <a class="ml-2 font-bold underline {{ $actionClasses }}" href="{{ $actionLink }}">{{ $actionText }}</a>
    @endif
</div>
