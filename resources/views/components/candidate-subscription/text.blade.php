@props(['name', 'label', 'value' => '', 'legend' => null, 'optional' => false])

<div {{ $attributes }}>
    <label for="{{ $name }}" class="font-bold text-base">{{ $label }}</label>
    <input
        {{ $optional === true ? '' : 'required' }}
        type="text"
        name="{{ $name }}"
        value="{{ $value }}"
        class="form-input block w-full"
    >
    @unless (empty($legend))
        <span class="text-xs italic">{{ $legend }}</span>
    @endunless
</div>
