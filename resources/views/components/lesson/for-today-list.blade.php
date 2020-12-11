<x-card.list.table-layout :title="$title">
    @php
        if ($listForInstructor()) {
            $headerClasses = [
                'class'         => 'col-span-5',
                'discipline'    => 'col-span-1',
                'registered'    => 'col-span-4',
                'actions'       => 'col-span-2',
            ];
        } else {
            $headerClasses = [
                'class'         => 'col-span-2',
                'discipline'    => 'col-span-2',
                'instructor'    => 'col-span-2',
                'registered'    => 'col-span-4',
                'actions'       => 'col-span-2',
            ];
        }

    @endphp
    <x-slot name="header">
        <x-card.list.table-header class="{{ $headerClasses['class'] }}" name="turma"/>
        <x-card.list.table-header class="{{ $headerClasses['discipline'] }}" name="disciplina"/>
        @unless ($listForInstructor())
        <x-card.list.table-header class="{{ $headerClasses['instructor'] }}" name="instrutor"/>
        @endunless
        <x-card.list.table-header class="{{ $headerClasses['registered'] }} text-center" name="registrada"/>
        <x-card.list.table-header class="{{ $headerClasses['actions'] }}" name=""/>
    </x-slot>

    <x-slot name="body">
        @foreach($lessons as $lesson)
        <x-card.list.table-row>
            <x-slot name="items">

                <time class="hidden" datetime="{{ $lesson->formatted_date }}"></time>

                <x-card.list.table-body-item class="{{ $headerClasses['class'] }}">
                    <x-slot name="item">
                        <span>{{ $lesson->formatted_course_classes }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="{{ $headerClasses['discipline'] }}">
                    <x-slot name="item">
                        <span>{{ $lesson->discipline }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                @unless($listForInstructor())
                <x-card.list.table-body-item class="{{ $headerClasses['instructor'] }}">
                    <x-slot name="item">
                        <span>{{ $lesson->instructor->name }}</span>
                    </x-slot>
                </x-card.list.table-body-item>
                @endunless

                <x-card.list.table-body-item class="{{ $headerClasses['registered'] }}">
                    <x-slot name="item">
                        <div class="flex items-center justify-center h-full">
                            <x-icons.active class="w-2 h-2" :active="$lesson->isRegistered()"/>
                        </div>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="{{ $headerClasses['actions'] }}">
                    <x-slot name="item">
                        <div class="flex justify-end space-x-2">
                            @if($showRegisterButton($lesson))
                            <a href="{{ route('lessons.register.create', ['lesson' => $lesson]) }}" class="text-gray-300 hover:text-blue-300">
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
