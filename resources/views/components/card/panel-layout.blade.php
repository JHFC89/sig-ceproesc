@props(['title'])

<div>
    <h2 class="text-xl font-medium text-gray-700 capitalize">{{ $title }}</h2>
    <div {{ $attributes->merge(['class' => 'mt-4 capitalize bg-white shadow rounded-md']) }}>
        {{ $content }}
    </div>
</div>
