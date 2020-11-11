@props(['title','header', 'body'])

<div>
    <h2 class="text-xl font-medium text-gray-700 capitalize">{{ $title }}</h2>

    <div class="mt-4 overflow-hidden capitalize shadow rounded-md">

        <div class="capitalize bg-gray-100 border-b">
            <div class="px-6 py-2 font-mono text-sm font-bold tracking-wide text-gray-600 uppercase grid-cols-12 grid">
            {{ $header }}
            </div>
        </div>

        <div class="capitalize bg-white divide-y">
            {{ $body }}
        </div>
    </div>
</div>
