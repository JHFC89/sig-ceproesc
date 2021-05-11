@props(['requests'])

<x-card.list.table-layout title="solicitações" {{ $attributes }}>

        <x-slot name="header">
            <x-card.list.table-header class="col-span-3" name="aula"/>
            <x-card.list.table-header class="col-span-4" name="tipo"/>
            <x-card.list.table-header class="col-span-3 text-center" name="situação"/>
            <x-card.list.table-header class="col-span-2" name=""/>
        </x-slot>

        <x-slot name="body">

            @foreach ($requests as $request)
            <x-card.list.table-row>
                <x-slot name="items">

                    <x-card.list.table-body-item class="flex items-center col-span-3">
                        <x-slot name="item">
                            <a href="{{ route('lessons.show', ['lesson' => $request->lesson]) }}" class="inline-block pr-2 font-medium text-blue-500 normal-case underline hover:text-blue-700">
                                {{ $request->lesson->formatted_date }}
                            </a>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-4">
                        <x-slot name="item">
                            <span>{{ $request->isRectification() ? 'retificação' : 'vencida' }}</span>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center justify-center col-span-3">
                        <x-slot name="item">
                            <x-badge :color="$request->isReleased() ? 'blue' : 'red'" :text="$request->isReleased() ? 'liberada' : 'em análise'"/>
                        </x-slot>
                    </x-card.list.table-body-item>

                    <x-card.list.table-body-item class="flex items-center col-span-2">
                        <x-slot name="item">
                            <div class="flex justify-end space-x-2 w-full">
                                <a 
                                    href="{{ route('requests.show', ['request' => $request]) }}"
                                    class="text-gray-300 hover:text-blue-300"
                                >
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
