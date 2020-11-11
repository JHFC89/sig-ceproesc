@props(['items'])
<div {{ $attributes->merge(['class' => 'px-6 py-4 text-base capitalize bg-white grid-cols-12 grid']) }}>
    {{ $items }}
</div>
