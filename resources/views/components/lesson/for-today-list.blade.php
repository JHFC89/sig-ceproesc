<x-card.list.table-layout :title="$title">
    <x-slot name="header">
        <x-card.list.table-header class="col-span-4" name="turma"/>
        <x-card.list.table-header class="col-span-2" name="disciplina"/>
        <x-card.list.table-header class="text-center col-span-4" name="registrada"/>
        <x-card.list.table-header class="col-span-2" name=""/>
    </x-slot>

    <x-slot name="body">
        @foreach($lessons as $lesson)
        <x-card.list.table-row>
            <x-slot name="items">

                <time class="hidden" datetime="{{ $lesson->formatted_date }}"></time>

                <x-card.list.table-body-item class="col-span-4">
                    <x-slot name="item">
                        <span>{{ $lesson->class }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="col-span-2">
                    <x-slot name="item">
                        <span>{{ $lesson->discipline }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="col-span-4">
                    <x-slot name="item">
                        <div class="flex items-center justify-center h-full">
                            <x-icons.active class="w-2 h-2" :active="$lesson->isRegistered()"/>
                        </div>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="col-span-2">
                    <x-slot name="item">
                        <div class="flex justify-end space-x-2">
                            @unless($lesson->isRegistered())
                            <a href="{{ route('lessons.register.create', ['lesson' => $lesson]) }}" class="text-gray-300 hover:text-blue-300">
                                <x-icons.register-lesson class="w-6"/>
                            </a>
                            @endunless
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
