@props(['name', 'label', 'value' => '', 'legend' => null])

<div {{ $attributes }}>
    <label for="{{ $name }}" class="font-bold text-base">{{ $label }}</label>
    <textarea required name="{{ $name }}" rows="3" class="form-textarea block w-full">{{ $value }}</textarea>
    @unless (empty($legend))
        <span class="text-xs italic">{{ $legend }}</span>
    @endunless
</div>
