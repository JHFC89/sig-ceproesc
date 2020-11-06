@props(['title'])

<div>
    <h2 class="text-xl font-medium text-gray-700 capitalize">{{ $title }}</h2>
    <div class="px-6 py-2 mt-4 capitalize bg-white shadow divide-y rounded-md">
        {{ $items }}
    </div>
</div>
