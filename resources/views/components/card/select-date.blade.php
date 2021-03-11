@props([
'dayName' => '', 
'monthName' => '', 
'yearName' => '', 
'wireDay' => '',
'wireMonth' => '',
'wireYear' => '',
])

<div class="space-x-4">
    <label class="inline-flex items-center space-x-2">
        <span>dia</span>
        <input 
            @if ($wireDay) wire:model="{{ $wireDay }}" @endif
            class="form-input block w-16"
            type="number"
            min="1"
            max="31"
            name="{{ $dayName }}"
            value="1"
            required
        >
    </label>
    <label class="inline-flex items-center space-x-2">
        <span>mês</span>
        <select 
            @if ($wireMonth) wire:model="{{ $wireMonth }}" @endif
            class="form-select"
            name="{{ $monthName }}"
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
            @if ($wireYear) wire:model="{{ $wireYear }}" @endif
            class="form-input block w-24"
            type="number"
            min="{{ now()->format('Y') }}"
            name="{{ $yearName }}"
            value="{{ now()->format('Y') }}"
            required
        >
    </label>
</div>
