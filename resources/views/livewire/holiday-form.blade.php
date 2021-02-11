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
                    <div class="space-x-4">
                        <label class="inline-flex items-center space-x-2">
                            <span>dia</span>
                            <input 
                                class="form-input block w-16"
                                type="number"
                                min="1"
                                max="31"
                                name="holidays[{{ $holiday }}][day]"
                                value="1"
                                required
                            >
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <span>mês</span>
                            <select 
                                class="form-select"
                                name="holidays[{{ $holiday }}][month]"
                                required
                            >
                                <option value="1">Janeiro</option>
                                <option value="2">Fevereiro</option>
                                <option value="3">Março</option>
                                <option value="4">Abril</option>
                                <option value="5">Maio</option>
                                <option value="6">Junho</option>
                                <option value="7">Julho</option>
                                <option value="8">Agosto</option>
                                <option value="9">Setembro</option>
                                <option value="10">Outrubro</option>
                                <option value="11">Novembro</option>
                                <option value="12">Dezembro</option>
                            </select>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <span>ano</span>
                            <input
                                class="form-input block w-24"
                                type="number"
                                min="{{ now()->format('Y') }}"
                                name="holidays[{{ $holiday }}][year]"
                                value="{{ now()->format('Y') }}"
                                requried
                            >
                        </label>
                    </div>
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
