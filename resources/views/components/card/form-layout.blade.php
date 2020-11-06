@props(['title', 'inputs'])

<div {{ $attributes }}>

    <h2 class="text-xl font-medium text-gray-700 capitalize">{{ $title }}</h2>

    <form class="mt-4 overflow-hidden capitalize bg-white shadow rounded-md">

        <div class="px-6 py-2 divide-y">
            {{ $inputs }}
        </div>

        <div class="flex justify-end px-6 py-4 bg-gray-100 space-x-2">
            {{ $footer }}
        </div>

    </form>
</div>
