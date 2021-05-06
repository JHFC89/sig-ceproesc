<div>
    <div class="flex w-full space-x-4">

        <x-card.list.table-layout class="w-1/2" title="disciplinas módulo básico">

            <x-slot name="header">

                <x-card.list.table-header class="col-span-4" name="nome"/>
                <x-card.list.table-header class="col-span-4" name="instrutor"/>
                <x-card.list.table-header class="col-span-2 text-center" name="carga horária"/>
                <x-card.list.table-header class="col-span-2 text-center" name="a preencher"/>

            </x-slot>

            <x-slot name="body">

                @foreach($courseClass->course->basicDisciplines() as $discipline)

                <x-card.list.table-row>
                    <x-slot name="items">

                        <x-card.list.table-body-item class="col-span-4">
                            <x-slot name="item">
                                <span class="flex items-center h-full">{{ $discipline->name }}</span>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="flex items-center col-span-4">
                            <x-slot name="item">
                                <div class="flex items-center h-full w-full">

                                    <select 
                                        wire:model="preSelectedInstructors.discipline-{{$discipline->id}}"
                                        class="form-select capitalize" 
                                        name="discipline" 
                                        required
                                    >
                                        <option class="text-gray-400" value="null" disabled>Escolha um instrutor</option>
                                        @foreach ($discipline->instructors as $instructor)
                                            <option 
                                                wire:key="{{$discipline->name . '-' . $instructor->id}}"
                                                value="{{ $instructor->id }}"
                                            >
                                                {{ $instructor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="flex items-center col-span-2">
                            <x-slot name="item">
                                <div class="flex items-center justify-center h-full w-full">
                                    <x-badge :text="$discipline->duration . ' hrs'" color="blue"/>
                                </div>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="flex items-center col-span-2">
                            <x-slot name="item">
                                <div class="flex items-center justify-center h-full w-full">
                                    @if ($this->durationDiff($discipline) > 0)
                                        <x-badge :text="$this->durationDiff($discipline) . ' hrs'" color="red"/>
                                    @elseif ($this->durationDiff($discipline) < 0)
                                        <x-badge :text="$this->durationDiff($discipline) . ' hrs'" color="red"/>
                                    @else
                                        <x-badge text="OK" color="blue"/>
                                    @endif
                                </div>
                            </x-slot>
                        </x-card.list.table-body-item>

                    </x-slot>

                </x-card.list.table-row>

                @endforeach

            </x-slot>

        </x-card.list.table-layout>

        <x-card.list.table-layout class="w-1/2" title="disciplinas módulo específico">

            <x-slot name="header">

                <x-card.list.table-header class="col-span-4" name="nome"/>
                <x-card.list.table-header class="col-span-4" name="instrutor"/>
                <x-card.list.table-header class="col-span-2 text-center" name="carga horária"/>
                <x-card.list.table-header class="col-span-2 text-center" name="a preencher"/>

            </x-slot>

            <x-slot name="body">

                @foreach($courseClass->course->specificDisciplines() as $discipline)

                <x-card.list.table-row>
                    <x-slot name="items">

                        <x-card.list.table-body-item class="col-span-4">
                            <x-slot name="item">
                                <span class="flex items-center h-full">{{ $discipline->name }}</span>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="flex items-center col-span-4">
                            <x-slot name="item">
                                <div class="flex items-center h-full w-full">

                                    <select 
                                        wire:model="preSelectedInstructors.discipline-{{$discipline->id}}"
                                        class="form-select capitalize" 
                                        name="discipline" 
                                        required
                                    >
                                        <option class="text-gray-400" value="null" disabled>Escolha um instrutor</option>
                                        @foreach ($discipline->instructors as $instructor)
                                            <option 
                                                wire:key="{{$discipline->name . '-' . $instructor->id}}"
                                                value="{{ $instructor->id }}"
                                            >
                                                {{ $instructor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="flex items-center col-span-2">
                            <x-slot name="item">
                                <div class="flex items-center justify-center h-full w-full">
                                    <x-badge :text="$discipline->duration . ' hrs'" color="blue"/>
                                </div>
                            </x-slot>
                        </x-card.list.table-body-item>

                        <x-card.list.table-body-item class="flex items-center col-span-2">
                            <x-slot name="item">
                                <div class="flex items-center justify-center h-full w-full">
                                    @if ($this->durationDiff($discipline) > 0)
                                        <x-badge :text="$this->durationDiff($discipline) . ' hrs'" color="red"/>
                                    @elseif ($this->durationDiff($discipline) < 0)
                                        <x-badge :text="$this->durationDiff($discipline) . ' hrs'" color="red"/>
                                    @else
                                        <x-badge text="OK" color="blue"/>
                                    @endif
                                </div>
                            </x-slot>
                        </x-card.list.table-body-item>


                    </x-slot>

                </x-card.list.table-row>

                @endforeach

            </x-slot>

        </x-card.list.table-layout>

    </div>
</div>
