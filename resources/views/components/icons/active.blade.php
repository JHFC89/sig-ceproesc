@props(['active' => false])

<div {{ $attributes }}>
    <span 
        class="block w-full h-full rounded-full 
            @if($active) bg-green-400 
            @else bg-red-400 
            @endif
        ">
    </span>
</div>
