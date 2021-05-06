<div>
    <div>
        <h2 class="text-xl font-medium text-gray-700 capitalize">Mês: {{ $month['id'] }}</h2>
        <div class="capitalize bg-gray-100 text-sm inline-flex w-40 rounded-md border divide-x shadow overflow-hidden mt-2">
            <a 
                href="#"
                wire:click.prevent="prevMonth"
                class="inline-block w-1/2 py-1 text-center hover:bg-gray-400 hover:text-gray-100 {{ $this->unavailableClassList('prev') }}"
            >
                anterior
            </a>
            <a
                href="#"
                wire:click.prevent="nextMonth"
                class="inline-block w-1/2 py-1 text-center hover:bg-gray-400 hover:text-gray-100 {{ $this->unavailableClassList('next') }}"
            >
                próximo
            </a>
        </div>
    </div>

    <div class="flex w-full space-x-4 mt-4">

        <x-card.list.table-layout class="w-1/2" title="primeiro horário">

            <x-slot name="header">

                <x-card.list.table-header class="col-span-2" name="data"/>
                <x-card.list.table-header class="col-span-1 text-center" name="c.h."/>
                <x-card.list.table-header class="col-span-4 text-center" name="disciplina"/>
                <x-card.list.table-header class="col-span-4 text-center" name="instrutor"/>
                <x-card.list.table-header class="col-span-1" name=""/>

            </x-slot>

            <x-slot name="body">

                @foreach($this->dates() as $date)

                    @if ($this->isExtraLesson($date))
                    <livewire:create-lesson-row 
                        :extra="true"
                        type="first"
                        :lesson="$this->lessonForDate($date, 'first', true)"
                        :date="$date"
                        :disciplines="$this->disciplines"
                        :preSelectedInstructors="$this->preSelectedInstructors"
                        :completedDisciplines="$this->completedDisciplines"
                        :duration="$this->calculateDuration($date, 'first')"
                        :key="'first-'.$date->format('Y-m-d')"
                    />
                    @else
                    <livewire:create-lesson-row 
                        type="first"
                        :lesson="$this->lessonForDate($date, 'first')"
                        :date="$date"
                        :disciplines="$this->disciplines"
                        :preSelectedInstructors="$this->preSelectedInstructors"
                        :completedDisciplines="$this->completedDisciplines"
                        :duration="$this->calculateDuration($date, 'first')"
                        :key="'first-'.$date->format('Y-m-d')"
                    />
                    @endif

                @endforeach

            </x-slot>

        </x-card.list.table-layout>

        <x-card.list.table-layout class="w-1/2" title="segundo horário">

            <x-slot name="header">

                <x-card.list.table-header class="col-span-2" name="data"/>
                <x-card.list.table-header class="col-span-1 text-center" name="c.h."/>
                <x-card.list.table-header class="col-span-4 text-center" name="disciplina"/>
                <x-card.list.table-header class="col-span-4 text-center" name="instrutor"/>
                <x-card.list.table-header class="col-span-1" name=""/>

            </x-slot>

            <x-slot name="body">

                @foreach($this->dates() as $date)

                    @if ($this->isExtraLesson($date))
                    <livewire:create-lesson-row 
                        :extra="true"
                        type="second"
                        :lesson="$this->lessonForDate($date, 'second', true)"
                        :date="$date"
                        :disciplines="$this->disciplines"
                        :preSelectedInstructors="$this->preSelectedInstructors"
                        :completedDisciplines="$this->completedDisciplines"
                        :duration="$this->calculateDuration($date, 'second')"
                        :key="'second-'.$date->format('Y-m-d')"
                    />
                    @else
                    <livewire:create-lesson-row 
                        type="second"
                        :lesson="$this->lessonForDate($date, 'second')"
                        :date="$date"
                        :disciplines="$this->disciplines"
                        :preSelectedInstructors="$this->preSelectedInstructors"
                        :completedDisciplines="$this->completedDisciplines"
                        :duration="$this->calculateDuration($date, 'second')"
                        :key="'second-'.$date->format('Y-m-d')"
                    />
                    @endif

                @endforeach

            </x-slot>

        </x-card.list.table-layout>

    </div>

    <div class="flex justify-end mt-4">
        <a
            wire:click.prevent="createLessons"
            href="#"
            @if ($createLessonsAvailable)
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            @else
                class="pointer-events-none px-4 py-2 text-sm font-medium leading-none text-blue-100 capitalize bg-blue-400 rounded-md shadown"
            @endif
        >
            cadastrar aulas
        </a>
    </div>

    @isset($createdLessons)
        <div class="fixed inset-0">
            <div class="w-full h-full bg-gray-900 opacity-75"></div>
            <div class="absolute inset-0 flex items-center justify-center">

                <div class="w-1/3 px-4 py-8 bg-gray-100 text-center rounded-md shadow">
                    <h3 class="text-2xl font-medium text-gray-700 capitalize">
                        Aulas cadastradas com sucesso!
                    </h3>
                    <p class="text-base font-medium text-gray-500">
                        Foram cadastradas <span class="text-blue-500 font-bold">{{ $createdLessons->count() }}</span> aulas no total e mais <span class="text-blue-500 font-bold">{{ $createdExtraLessons->count() }}</span> aulas extras.
                    </p>
                    <a
                        href="{{ route('classes.lessons.index', ['courseClass' => $courseClass]) }}"
                        class="inline-block mt-8 px-4 py-2 shadow-md text-base font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
                    >entendi</a>
                </div>

            </div>
        </div>
    @endisset

    <div wire:loading wire:target="createLessons" class="fixed inset-0">
        <div class="w-full h-full bg-gray-900 opacity-75"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-1/3 px-4 py-8 bg-gray-100 text-center rounded-md shadow">
                <h3 class="text-2xl font-medium text-gray-700 capitalize">Cadastrando as aulas<h3>
                <p class="text-base font-medium text-gray-400">Por favor, aguarde...</p>
            </div>
        </div>
    </div>

    @if ($showDisciplineCompletedMessage)
    <div class="fixed inset-0">
        <div class="w-full h-full bg-gray-900 opacity-75"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-1/3 px-4 py-8 bg-gray-100 text-center rounded-md shadow">
                <p class="text-base font-medium text-gray-400">
                    Disciplina finalizada:
                </p>
                <h3 class="text-2xl font-medium text-gray-700 capitalize">
                    {{ $this->getCompletedDiscipline()->name }}
                </h3>
                <button
                    class="inline-block mt-8 px-4 py-2 shadow-md text-base font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
                    type="button"
                    wire:click="hideDisciplineCompletedMessage"
                >
                    Entendi
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
