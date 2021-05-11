@if ($hasLesson)
<x-card.list.table-layout :title="$title" class="hidden lg:block">
    <x-slot name="header">
        <x-card.list.table-header class="{{ $columnSize['class'] }}" name="turma"/>
        <x-card.list.table-header class="{{ $columnSize['discipline'] }}" name="disciplina"/>
        @unless ($listForInstructor())
        <x-card.list.table-header class="{{ $columnSize['instructor'] }}" name="instrutor"/>
        @endunless
        @unless ($hideRegistered)
        <x-card.list.table-header class="{{ $columnSize['registered'] }} text-center" name="registrada"/>
        @endunless
        <x-card.list.table-header class="{{ $columnSize['actions'] }}" name=""/>
    </x-slot>

    <x-slot name="body">
        @foreach($lessons as $lesson)
        <x-card.list.table-row>
            <x-slot name="items">

                <time class="hidden" datetime="{{ $lesson->formatted_date }}"></time>

                <x-card.list.table-body-item class="{{ $columnSize['class'] }}">
                    <x-slot name="item">
                        <span>{{ $showClasses($lesson) }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="{{ $columnSize['discipline'] }}">
                    <x-slot name="item">
                        <span>{{ $lesson->discipline->name }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                @unless($listForInstructor())
                <x-card.list.table-body-item class="{{ $columnSize['instructor'] }}">
                    <x-slot name="item">
                        <span>{{ $lesson->instructor->name }}</span>
                    </x-slot>
                </x-card.list.table-body-item>
                @endunless

                @unless ($hideRegistered)
                <x-card.list.table-body-item class="{{ $columnSize['registered'] }}">
                    <x-slot name="item">
                        <div class="flex items-center justify-center h-full">
                            <x-icons.active class="w-2 h-2" :active="$lesson->isRegistered()"/>
                        </div>
                    </x-slot>
                </x-card.list.table-body-item>
                @endunless

                <x-card.list.table-body-item class="{{ $columnSize['actions'] }}">
                    <x-slot name="item">
                        <div class="flex justify-end space-x-2">
                            @if($showRegisterButton($lesson))
                            <a href="{{ route('lessons.registers.create', ['lesson' => $lesson]) }}" class="text-gray-300 hover:text-blue-300">
                                <x-icons.register-lesson class="w-6"/>
                            </a>
                            @endif
                            <a href="{{ route('lessons.show', ['lesson' => $lesson]) }}" class="text-gray-300 hover:text-blue-300">
                                <x-icons.see class="w-6"/>
                            </a>
                        </div>
                    </x-slot>
                </x-card.list.table-body-item>

            </x-slot>
        </x-card.list.table-row>
        @endforeach
    </x-slot>
</x-card.list.table-layout>
@elseif ($alwaysShow)
<div> 
    <x-card.panel-layout :title="$title">
        <x-slot name="content">
        </x-slot>
    </x-card.panel-layout>
</div>
@endif

@if ($hasLesson)
<div class="lg:hidden">
    <x-card.list.description-layout :title="$title">

        <x-slot name="items">

            @foreach($lessons as $lesson)
            <x-card.list.description-item
                :label="$lesson->formatted_date"
                type="link"
                :href="route('lessons.show', ['lesson' => $lesson])"
                :description="$lesson->discipline->name"
            />
            @endforeach

        </x-slot>

    </x-card.list.description-layout>
</div>
@endif
