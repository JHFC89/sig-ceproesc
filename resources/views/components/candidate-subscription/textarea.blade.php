@props(['name', 'label', 'value' => ''])

<div {{ $attributes }}>
    <label for="{{ $name }}" class="font-bold text-base">{{ $label }}</label>
    <textarea required name="{{ $name }}" rows="3" class="form-textarea block w-full">{{ $value }}</textarea>
</div>
