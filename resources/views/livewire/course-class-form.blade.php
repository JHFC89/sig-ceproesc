<div x-data> 

    <x-card.form-layout 
        title="cadastrar nova turma" 
        :single="false"
        wire:submit.prevent="submit"
    >

        <x-slot name="panels">

            <x-card.panel-layout title="cadastrar nova turma" class="px-6 py-2 divide-y">
                <x-slot name="content">

                    <x-card.form-input name="name" label="nome">
                        <x-slot name="input">
                            <input 
                                wire:model="class.name"
                                class="block w-full form-input @error('class.name')border-red-500 @enderror" 
                                name="name" 
                                placeholder="Digite o nome da turma"
                                required
                            >
                            @error('class.name')
                                <span class="block text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="city" label="cidade">
                        <x-slot name="input">
                            <select 
                                wire:model="class.city"
                                class="form-select capitalize @error('course')border-red-500 @enderror" 
                                name="city" 
                                required
                            >
                                <option class="text-gray-400" selected disabled>Escolha uma cidade</option>
                                @foreach (App\Models\CitiesList::LIST as $city)
                                    <option 
                                        class="capitalize" 
                                        value="{{ $city }}"
                                    >
                                        {{ $city }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class.city')
                                <span class="block text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="course" label="programa">
                        <x-slot name="input">
                            <div class="flex">
                                <select 
                                    wire:change="selectCourse($event.target.value)"
                                    class="form-select @error('course')border-red-500 @enderror"
                                    name="course"
                                    required
                                >
                                    <option class="text-gray-400" selected disabled>Escolha um programa</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                                @if (! empty($this->course))
                                    <a 
                                        href="{{ route('courses.show', ['course' => $course]) }}" 
                                        class="ml-2 text-gray-300 flex items-center align-center hover:text-blue-300"
                                        target="_blank"
                                    >
                                        <x-icons.see class="w-6"/>
                                    </a>
                                @endif
                            </div>
                            @error('course')
                                <span class="block text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="basic disciplines duration" label="carga horária total">
                        <x-slot name="input">
                            <input 
                                class="block w-20 form-input bg-gray-100" 
                                type="number"
                                value="{{ $duration }}"
                                disabled
                            >
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="basic disciplines duration" label="carga horária módulo básico">
                        <x-slot name="input">
                            <input 
                                class="block w-20 form-input bg-gray-100" 
                                type="number"
                                value="{{ $basicDuration }}"
                                disabled
                            >
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="specific disciplines duration" label="carga horária módulo específico">
                        <x-slot name="input">
                            <input 
                                disabled
                                class="block w-20 form-input bg-gray-100" 
                                type="number"
                                value="{{ $specificDuration }}"
                                disabled
                            >
                        </x-slot>
                    </x-card.form-input>

                </x-slot>
            </x-card.panel-layout>

            <x-card.panel-layout title="duração e férias" class="px-6 py-2 divide-y">
                <x-slot name="content">

                    <x-card.form-input name="begin" label="dia de início">
                        <x-slot name="input">
                            <x-card.select-date
                                wireDay="class.begin.day"
                                wireMonth="class.begin.month"
                                wireYear="class.begin.year"
                            />
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="end" label="dia do término">
                        <x-slot name="input">
                            <x-card.select-date
                                wireDay="class.end.day"
                                wireMonth="class.end.month"
                                wireYear="class.end.year"
                            />
                            @error('duration')
                                <span class="mt-2 block text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="intro_begin" label="dia de início da introdução">
                        <x-slot name="input">
                            <x-card.select-date
                                :disabled="true"
                                wireDay="class.begin.day"
                                wireMonth="class.begin.month"
                                wireYear="class.begin.year"
                            />
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="intro_end" label="dia do término da introdução">
                        <x-slot name="input">
                            <x-card.select-date
                                wireDay="class.intro_end.day"
                                wireMonth="class.intro_end.month"
                                wireYear="class.intro_end.year"
                            />
                            @error('intro_end')
                                <span class="mt-2 block text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </x-slot>
                    </x-card.form-input>


                    <x-card.form-input name="vacation_begin" label="dia de início das férias">
                        <x-slot name="input">
                            <x-card.select-date
                                wireDay="class.vacation_begin.day"
                                wireMonth="class.vacation_begin.month"
                                wireYear="class.vacation_begin.year"
                            />
                            @error('vacation_begin')
                                <span class="mt-2 block text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="vacation_end" label="dia do término das férias">
                        <x-slot name="input">
                            <x-card.select-date
                                wireDay="class.vacation_end.day"
                                wireMonth="class.vacation_end.month"
                                wireYear="class.vacation_end.year"
                            />
                            @error('vacation_duration')
                                <span class="mt-2 block text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </x-slot>
                    </x-card.form-input>

                </x-slot>
            </x-card.panel-layout>

            <x-card.panel-layout title="aulas" class="px-6 py-2 divide-y">
                <x-slot name="content">

                    <x-card.form-input name="first_day" label="primeiro dia da semana de aula teórica">
                        <x-slot name="input">
                            <div class="space-x-4">

                                <label class="inline-flex items-center space-x-2">
                                    <span>Dia</span>
                                    <select 
                                        wire:model="class.first_day"
                                        class="form-select capitalize @error('class.first_day')border-red-500 @enderror"
                                        name="first_day"
                                        required
                                    >
                                        <option value="monday">segunda-feira</option>
                                        <option value="tuesday">terça-feira</option>
                                        <option value="wednesday">quarta-feira</option>
                                        <option value="thursday">quinta-feira</option>
                                        <option value="friday">sexta-feira</option>
                                    </select>
                                </label>

                                <label class="inline-flex items-center space-x-2">
                                    <span>Carga Horária</span>
                                    <input 
                                        wire:model="class.first_day_duration"
                                        class="block w-20 form-input @error('class.first_day_duration')border-red-500 @enderror" 
                                        name="first_day_duration"
                                        type="number"
                                        value="4"
                                        min="1"
                                        max="8"
                                    >
                                </label>
                                @error('class.first_day')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
                                @error('class.first_day_duration')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror

                            </div>
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="second_day" label="segundo dia da semana de aula teórica">
                        <x-slot name="input">
                            <div class="space-x-4">

                                <label class="inline-flex items-center space-x-2">
                                    <span>Dia</span>
                                    <select 
                                        wire:model="class.second_day"
                                        class="form-select capitalize @error('class.second_day')border-red-500 @enderror"
                                        name="second_day"
                                        required
                                    >
                                        <option value="monday">segunda-feira</option>
                                        <option value="tuesday">terça-feira</option>
                                        <option value="wednesday">quarta-feira</option>
                                        <option value="thursday">quinta-feira</option>
                                        <option value="friday">sexta-feira</option>
                                        <option value="saturday">sábado</option>
                                    </select>
                                </label>

                                <label class="inline-flex items-center space-x-2">
                                    <span>Carga Horária</span>
                                    <input 
                                        wire:model="class.second_day_duration"
                                        class="block w-20 form-input @error('class.second_day_duration')border-red-500 @enderror" 
                                        name="second_day_duration"
                                        type="number"
                                        value="5"
                                    >
                                </label>
                                @error('class.second_day')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror
                                @error('class.second_day_duration')
                                    <span class="block text-sm text-red-500">{{ $message }}</span>
                                @enderror

                            </div>
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="practical_duration" label="carga horária atividade prática">
                        <x-slot name="input">
                            <input 
                                wire:model="practicalDuration"
                                class="block w-20 form-input @error('practicalDuration')border-red-500 @enderror" 
                                type="number"
                                value="{{ $practicalDuration }}"
                                min="1"
                                max="6"
                            >
                            @error('practicalDuration')
                                <span class="block text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="theoretical_duration" label="total carga horária aprendizagem teórica">
                        <x-slot name="input">
                            <input 
                                class="inline-block w-20 form-input bg-gray-100 @error('theoretical_duration')border-red-500 @enderror" 
                                type="number"
                                value="{{ $this->totalTheoreticalDuration() }}"
                                disabled
                            >
                            <div class="inline-block text-red-500 pl-2 text-sm">
                                @if ($this->theoreticalDurationDiff() > 0)
                                    <span>
                                        *Faltam <span>{{ $this->theoreticalDurationDiff() }}</span> horas</span>
                                    </span>
                                @elseif ($this->theoreticalDurationDiff() < 0)
                                    <span>
                                        *Sobraram <span>{{ $this->theoreticalDurationDiff() * -1 }}</span> horas</span>
                                    </span>
                                @endif
                            </div>
                            @error('theoretical_duration')
                                <span class="block text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </x-slot>
                    </x-card.form-input>

                    <x-card.form-input name="practical_duration" label="total carga horária atividade prática">
                        <x-slot name="input">
                            <input 
                                class="block w-20 form-input bg-gray-100" 
                                type="number"
                                value="{{ $this->calculateTotalPracticalDuration() }}"
                                disabled
                            >
                        </x-slot>
                    </x-card.form-input>

                </x-slot>
            </x-card.panel-layout>

            @if ($showSchedule)
                <x-card.panel-layout title="cronograma" wire:loading.class="opacity-50" class="px-6 py-2 divide-y transition-opacity duration-200">
                    <x-slot name="content">

                        <x-course-class.schedule 
                            class="grid grid-cols-3 gap-6 py-4"
                            wire:click="toggleOffday($event.target.attributes.datetime.value)"
                            :group="$courseClass"
                            :offdays="$this->offdayDates()"
                        />

                    </x-slot>
                </x-card.panel-layout>
            @endif
        </x-slot>

        <x-slot name="footer">

            @if (! $showSchedule)
                <button 
                    wire:click="generateSchedule"
                    type="button"
                    class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
                >
                    gerar cronograma
                </button>
            @else
                <button 
                    type="submit"
                    class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
                >
                    cadastrar turma
                </button>
            @endif

        </x-slot>

    </x-card.form-layout>
</div>
