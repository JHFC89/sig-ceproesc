@props(['name', 'label', 'options', 'value' => [],'case' => 'capitalize'])

<div
    x-data="{inputs: null}"
    x-init="
        inputs = $el.querySelectorAll('input')
        if (Array.prototype.slice.call(inputs).some(i => i.checked)) {
            inputs.forEach(i => i.removeAttribute('required'))
        }
    "
    x-on:change="
        if (Array.prototype.slice.call(inputs).some(i => i.checked)) {
            inputs.forEach(i => i.removeAttribute('required'))
        } else {
            inputs.forEach(i => i.setAttribute('required', true))
        }
    "
    {{ $attributes }}
>

    <h3 class="font-bold text-base">{{ $label }}</h3>

    @foreach ($options as $option)
    <div>
        <label class="inline-flex items-center">
            <input
                required
                type="checkbox"
                name="{{ $name }}"
                value="{{ $option }}"
                @if (in_array($option, $value))
                    checked
                @endif
                class="form-checkbox"
            >
            <span class="ml-2 {{ $case }}">{{ $option }}</span>
        </label>
    </div>
    @endforeach

</div>
