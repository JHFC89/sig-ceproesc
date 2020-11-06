@props(['label', 'description'])

<div class="flex items-center py-4">
    <div class="w-1/4">
        <span class="text-gray-600">{{ $label }}</span>
    </div>
    <div class="w-3/4">
        <span class="inline-block pr-2 font-medium">{{ $description }}</span>
    </div>
</div>
