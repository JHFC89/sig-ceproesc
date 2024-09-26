@props(['name', 'label', 'options', 'value' => '', 'legend' => null, 'optional' => false])

<div {{ $attributes }} >

    <h3 class="font-bold text-base">{{ $label }}</h3>

    @foreach ($options as $option)
    <div>
        <label class="inline-flex items-center">
            <input
                {{ $optional === true ? '' : 'required' }}
                type="radio"
                name="{{ $name }}"
                value="{{ $option }}"
                @if ($option == $value)
                    checked
                @endif
                class="form-radio"
            >
            <span class="ml-2 capitalize">{{ $option }}</span>
        </label>
    </div>
    @endforeach

    @unless (empty($legend))
        <span class="text-xs italic">{{ $legend }}</span>
    @endunless
</div>
