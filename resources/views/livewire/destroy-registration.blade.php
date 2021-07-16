<div>
    @if ($this->authorized())
    <button
        type="button"
        class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-red-600 hover:bg-red-500 hover:text-red-100 rounded-md shadown"
        wire:click="destroy"
    >
        cancelar registro
    </button>
    @endif

    @if ($this->showConfirmation())
    <div class="z-10 fixed inset-0">
        <div class="w-full h-full bg-gray-900 opacity-75"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-1/3 px-4 py-8 bg-gray-100 text-center rounded-md shadow">
                <p class="text-base font-medium capitalize text-gray-400">
                    cancelar registro
                </p>

                @if ($this->confirmation())
                <p class="mt-4">Clique em "Confirmar" para cancelar o registro.</p>

                <div class="pt-8">
                    <button
                        class="inline-block px-4 py-2 shadow-md text-base font-medium leading-none text-white capitalize bg-red-600 hover:bg-red-500 hover:text-red-100 rounded-md shadown"
                        type="button"
                        wire:click="abort"
                    >
                        desistir
                    </button>
                    <button
                        class="inline-block px-4 py-2 shadow-md text-base font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
                        type="button"
                        wire:click="confirm"
                    >
                        confirmar
                    </button>
                </div>
                @endif

                @if ($this->success())
                <p class="mt-4">Registro cancelado com sucesso!</p>
                <div class="pt-8">
                    <a
                        href="{{route('dashboard')}}"
                        class="inline-block px-4 py-2 shadow-md text-base font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
                    >
                        Entendi
                    </a>
                </div>

                @elseif ($this->failed())
                <p class="mt-4">Ocorreu um erro e o registro não foi cancelado.</p>
                <div class="pt-8">
                    <button
                        wire:click="abort"
                        class="inline-block px-4 py-2 shadow-md text-base font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
                        type="button"
                    >
                        Entendi
                    </button>
                </div>
                @endif
        </div>
    </div>
    @endif
</div>
