@props(['name', 'label', 'input'])

<div class="flex flex-col items-center py-4 space-y-1 lg:flex-row">

    <label for="{{ $name }}" class="font-medium lg:font-normal lg:w-1/4">
        <span class="text-gray-600">{{ $label }}</span>
    </label>

    <div class="w-full lg:w-2/4">
        {{ $input }}
    </div>

</div>
