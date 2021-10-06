@props(['name', 'label', 'options', 'value' => ''])

<div {{ $attributes }}>
    <label for="{{ $name }}" class="font-bold text-base">{{ $label }}</label>
    <select required name="{{ $name }}" class="form-select block w-full capitalize">
        @foreach ($options as $option)
            <option
                value="{{ $option }}"
                @if ($option == $value)
                    selected
                @endif
                class="capitalize"
            >
                {{ $option }}
            </option>
        @endforeach
    </select>
</div>
