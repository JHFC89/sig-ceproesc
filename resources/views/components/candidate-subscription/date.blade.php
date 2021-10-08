@props(['name', 'label', 'value' => '', 'legend' => null])

<div>
    <label for="{{ $name }}" class="font-bold text-base">{{ $label }}</label>
    <input
        required
        type="date"
        name="{{ $name }}"
        value="{{ $value }}"
        class="form-input block w-full"
    >
    @unless (empty($legend))
        <span class="text-xs italic">{{ $legend }}</span>
    @endunless
</div>
