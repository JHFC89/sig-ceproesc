@props(['name', 'label', 'value' => '', 'min' => 0, 'max' => 9999])

<div {{ $attributes }}>
    <label for="{{ $name }}" class="font-bold text-base">{{ $label }}</label>
    <input
        required
        type="number"
        name="{{ $name }}"
        min="{{ $min }}"
        max="{{ $max }}"
        value="{{ $value }}"
        class="form-input block w-full"
    >
</div>
