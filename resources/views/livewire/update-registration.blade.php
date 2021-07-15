<div>
    <x-card.list.description-item
        :label="$label"
        :type="$type"
        :description="$registration->{$property}"
    >
        <div class="absolute right-0 flex items-center">
            @if ($this->updatable())
            <button type="button" wire:click="toggleEdit" class="text-gray-300 hover:text-blue-300">
                <x-icons.edit class="w-6"/>
            </button>
            @endif
        </div>
    </x-card.list.description-item>

    @if ($editing)
    <div class="z-10 fixed inset-0">
        <div class="w-full h-full bg-gray-900 opacity-75"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-1/3 px-4 py-8 bg-gray-100 text-center rounded-md shadow">
                <p class="text-base font-medium text-gray-400">
                    atualizar {{$label}}
                </p>
                <input type="text" model="{{$this->fieldName()}}" placeholder="{{$this->placeholder()}}" wire:model="{{$this->fieldName()}}" class="form-input block w-full mt-4 @error($this->fieldName()) border-red-500 @enderror"></input>
                <x-validation-error name="{{$this->fieldName()}}"/>
                <div class="pt-4">
                    <button
                        class="inline-block px-4 py-2 shadow-md text-base font-medium leading-none text-white capitalize bg-red-600 hover:bg-red-500 hover:text-red-100 rounded-md shadown"
                        type="button"
                        wire:click="toggleEdit"
                    >
                        cancelar
                    </button>
                    <button
                        wire:click="update"
                        class="inline-block px-4 py-2 shadow-md text-base font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
                        type="button"
                    >
                        salvar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
