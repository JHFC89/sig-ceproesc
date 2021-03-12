<div>
    <x-card.form-layout 
        title="cadastrar nova disciplina" 
        :action="route('holidays.store')"
        method="POST"
    >

        <x-slot name="inputs">

        @for ($holiday = 0; $holiday < $count + 1; $holiday++)

            <x-card.form-input name="name" label="nome">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea" 
                        name="holidays[{{ $holiday }}][name]" 
                        placeholder="Digite o nome do feriado"
                        required
                    >
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="date" label="data">
                <x-slot name="input">
                    <x-card.select-date
                        :dayName="'holidays[' . $holiday . '][day]'"
                        :monthName="'holidays[' . $holiday . '][month]'"
                        :yearName="'holidays[' . $holiday . '][year]'"
                    />
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="local" label="local">
                <x-slot name="input">
                    <select 
                        class="form-select capitalize" 
                        name="holidays[{{ $holiday }}][local]" 
                    >
                        <option value="">Nacional</option>
                        @foreach (App\Models\CitiesList::LIST as $city)
                            <option 
                                class="capitalize" 
                                value="{{ $city }}"
                            >
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </x-slot>
            </x-card.form-input>

        @endfor

        </x-slot>

        <x-slot name="footer">
            <button 
                wire:click="increment"
                type="button"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                adicionar feriado
            </button>
            <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                cadastrar feriados
            </button>
        </x-slot>

    </x-card.form-layout>
</div>
