<x-card.list.table-layout :title="$title">
    <x-slot name="header">
        <x-card.list.table-header class="col-span-2" name="data"/>
        <x-card.list.table-header class="col-span-2" name="turma"/>
        <x-card.list.table-header class="col-span-2" name="disciplina"/>
        <x-card.list.table-header class="text-center col-span-2" name="registrada"/>
        <x-card.list.table-header class="col-span-4" name=""/>
    </x-slot>

    <x-slot name="body">
        @foreach($lessons as $lesson)
        <x-card.list.table-row>
            <x-slot name="items">

                <x-card.list.table-body-item class="col-span-2">
                    <x-slot name="item">
                        <span>{{ $lesson->formatted_date }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="col-span-2">
                    <x-slot name="item">
                        <span>{{ $lesson->class }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="col-span-2">
                    <x-slot name="item">
                        <span>{{ $lesson->discipline }}</span>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="col-span-2">
                    <x-slot name="item">
                        <div class="flex items-center justify-center h-full">
                            <span 
                                class="block w-2 h-2 rounded-full 
                                    @if($lesson->isRegistered()) bg-green-400 
                                    @else bg-red-400 
                                    @endif
                                ">
                            </span>
                        </div>
                    </x-slot>
                </x-card.list.table-body-item>

                <x-card.list.table-body-item class="col-span-4">
                    <x-slot name="item">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('lessons.register.create', ['lesson' => $lesson]) }}" class="text-gray-300 hover:text-blue-300">
                                <x-icons.register-lesson class="w-6"/>
                            </a>
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
