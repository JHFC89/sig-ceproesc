<x-card.list.table-row>

    <x-slot name="items">

        <x-card.list.table-body-item class="col-span-2">
            <x-slot name="item">
                <span class="flex items-center h-full">{{ $date->format('d-m-Y') }}</span>
            </x-slot>
        </x-card.list.table-body-item>

        <x-card.list.table-body-item class="col-span-1">
            <x-slot name="item">
                <div class="flex items-center h-full">
                    <x-badge :text="$duration . ' Hrs'" :color="$this->extra ? 'red' : 'blue'"/>
                </div>
            </x-slot>
        </x-card.list.table-body-item>

        <x-card.list.table-body-item class="flex items-center col-span-4">
            <x-slot name="item">
                <div class="flex items-center justify-center h-full w-full">

                    <select 
                        wire:model="selectedDiscipline"
                        class="form-select capitalize" 
                        name="discipline" 
                        required
                    >
                        <option class="text-gray-400" value="null" disabled>Escolha uma disciplina</option>
                        @foreach ($disciplines as $discipline)
                            @if ($this->isCompleted($discipline->id))
                                <option 
                                    wire:key="{{ 'discipline-' . $discipline->id}}"
                                    value="{{ $discipline->id }}"
                                        disabled
                                        class="text-gray-400 bg-gray-100"
                                >
                                    {{ $discipline->name }}
                                </option>
                            @else
                                <option 
                                    wire:key="{{ 'discipline-' . $discipline->id}}"
                                    value="{{ $discipline->id }}"
                                >
                                    {{ $discipline->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </x-slot>
        </x-card.list.table-body-item>

        <x-card.list.table-body-item class="flex items-center col-span-4">
            <x-slot name="item">
                <div class="flex items-center justify-center h-full w-full">

                    @if ($selectedDiscipline)
                        <select 
                            wire:model="selectedInstructor"
                            class="form-select capitalize @if($selectedInstructor == null)border-red-500 @endif @error('instructor')border-red-500 @enderror" 
                            name="instructor" 
                            required
                        >
                            <option class="text-gray-400" value="null" disabled>Escolha um instrutor</option>
                            @foreach ($instructors as $instructor)
                                <option 
                                    wire:key="{{ 'instructor-' . $instructor->id}}"
                                    class="capitalize" 
                                    value="{{ $instructor->id }}"
                                >
                                    {{ $instructor->name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <select 
                            wire:model="selectedInstructor"
                            class="form-select capitalize bg-gray-100" 
                            name="instructor" 
                            disabled
                        >
                            <option class="text-gray-400" value="null" selected disabled>Escolha uma disciplina</option>
                        </select>
                    @endif

                </div>
            </x-slot>
        </x-card.list.table-body-item>

        <x-card.list.table-body-item class="col-span-1">
            <x-slot name="item">
                <div class="flex items-center justify-center h-full">
                    @if ($this->isUpdating())
                        <button type="button" wire:click="resetLesson">
                            <x-icons.edit class="w-6 text-gray-300 hover:text-blue-300"/>
                        </button>
                    @else
                        <button type="button" disabled wire:click="resetLesson">
                            <x-icons.edit class="cursor-default w-6 text-gray-100"/>
                        </button>
                    @endif
                </div>
            </x-slot>
        </x-card.list.table-body-item>

    </x-slot>

</x-card.list.table-row>
