@props([
'dayName' => '', 
'monthName' => '', 
'yearName' => '', 
'wireDay' => '',
'wireMonth' => '',
'wireYear' => '',
'disabled' => false,
'minYear' => true,
'dayValue' => 1,
'monthValue' => 1,
'yearValue' => now()->format('Y'),
])

<div class="flex flex-col space-y-2 lg:space-y-0 lg:flex-row lg:space-x-4">
    <label class="inline-flex items-center space-x-2">
        <span class="w-10 lg:w-auto">dia</span>
        <input 
            @if ($wireDay) wire:model="{{ $wireDay }}" @endif
            @if ($disabled) disabled @endif
            class="form-input block w-16 @if ($disabled) bg-gray-100 @endif"
            type="number"
            min="1"
            max="31"
            name="{{ $dayName }}"
            value="{{ $dayValue }}"
            required
        >
    </label>
    <label class="inline-flex items-center space-x-2">
        <span class="w-10 lg:w-auto">mês</span>
        <select 
            @if ($wireMonth) wire:model="{{ $wireMonth }}" @endif
            @if ($disabled) disabled @endif
            class="form-select @if ($disabled) bg-gray-100 @endif"
            name="{{ $monthName }}"
            required
        >
            <option value="1" @if ($monthValue == 1) selected @endif>Janeiro</option>
            <option value="2" @if ($monthValue == 2) selected @endif>Fevereiro</option>
            <option value="3" @if ($monthValue == 3) selected @endif>Março</option>
            <option value="4" @if ($monthValue == 4) selected @endif>Abril</option>
            <option value="5" @if ($monthValue == 5) selected @endif>Maio</option>
            <option value="6" @if ($monthValue == 6) selected @endif>Junho</option>
            <option value="7" @if ($monthValue == 7) selected @endif>Julho</option>
            <option value="8" @if ($monthValue == 8) selected @endif>Agosto</option>
            <option value="9" @if ($monthValue == 9) selected @endif>Setembro</option>
            <option value="10" @if ($monthValue == 10) selected @endif>Outrubro</option>
            <option value="11" @if ($monthValue == 11) selected @endif>Novembro</option>
            <option value="12" @if ($monthValue == 12) selected @endif>Dezembro</option>
        </select>
    </label>
    <label class="inline-flex items-center space-x-2">
        <span class="w-10 lg:w-auto">ano</span>
        <input
            @if ($wireYear) wire:model="{{ $wireYear }}" @endif
            @if ($disabled) disabled @endif
            class="form-input block w-24 @if ($disabled) bg-gray-100 @endif"
            type="number"
            min="{{ $minYear ? now()->format('Y') : '1900' }}"
            name="{{ $yearName }}"
            value="{{ $yearValue }}"
            required
        >
    </label>
</div>
