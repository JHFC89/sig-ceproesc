<x-card.list.table-layout :title="$title">
    <x-slot name="header">
        <x-card.list.table-header class="{{ $headerClasses['date'] }}" name="data"/>
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

                <x-card.list.table-body-item class="{{ $headerClasses['date'] }}">
                    <x-slot name="item">
                        <span>{{ $lesson->formatted_date }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="{{ $headerClasses['class'] }}">
                    <x-slot name="item">
                        <span>{{ $showClasses($lesson) }}</span>
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
                            @if($showExpiredWarning($lesson))
                            <span class="inline-block px-2 py-1 text-sm font-medium leading-none text-red-700 bg-red-100 rounded-md">vencida</span>
                            @elseif($showOpenRequestWarning($lesson))
                            <span class="inline-block px-2 py-1 text-sm font-medium leading-none text-yellow-700 bg-yellow-100 rounded-md">em análise</span>
                            @else
                            <x-icons.active class="w-2 h-2" :active="$lesson->isRegistered()"/>
                            @endif
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
                            @if($showExpiredWarning($lesson))
                            <a href="{{ route('lessons.requests.create', ['lesson' => $lesson]) }}" class="text-red-300 hover:text-red-400">
                                <x-icons.exclamation class="w-6"/>
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
