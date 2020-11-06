@props(['name', 'label', 'input'])

<div class="flex items-center py-4">

    <label for="{{ $name }}" class="w-1/4">
        <span class="text-gray-600">{{ $label }}</span>
    </label>

    <div class="w-2/4">
        {{ $input }}
    </div>

</div>
