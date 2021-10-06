@props(['name', 'label', 'value' => ''])

<div {{ $attributes }}>
    <label for="{{ $name }}" class="font-bold text-base">{{ $label }}</label>
    <input
        required
        type="email"
        name="{{ $name }}"
        value="{{ $value }}"
        class="form-input block w-full"
    >
</div>
