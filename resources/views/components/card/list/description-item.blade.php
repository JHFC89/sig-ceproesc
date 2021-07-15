@props(['label', 'description', 'type' => 'title', 'href' => '', 'linebreak' => false])

<div class="relative flex items-center py-4">
    <div class="w-1/3 lg:w-1/4">
        <span class="text-gray-600">{{ $label }}</span>
    </div>
    <div class="w-2/3 ml-4 lg:ml-0 lg:w-3/4">
        @if($type == 'link')
            <a href="{{ $href }}" class="inline-block pr-2 font-medium text-blue-500 normal-case underline hover:text-blue-700">{{ $description }}</a>
        @else
            @if ($linebreak)
            <span class="inline-block pr-2 font-medium {{ $type == 'text' ? 'normal-case' : '' }}">{!! nl2br($description) !!}</span>
            @else
            <span class="inline-block pr-2 font-medium {{ $type == 'text' ? 'normal-case' : '' }}">{{ $description }}</span>
            @endif
        @endif
    </div>
    {{ $slot }}
</div>
