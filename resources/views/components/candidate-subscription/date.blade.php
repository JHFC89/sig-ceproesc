@props(['name', 'label', 'value' => ''])

<div>
    <label for="{{ $name }}" class="font-bold text-base">{{ $label }}</label>
    <input
        required
        type="date"
        name="{{ $name }}"
        value="{{ $value }}"
        class="form-input block w-full"
    >
</div>
