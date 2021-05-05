@props (['name'])

@error($name)
    <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
@enderror
