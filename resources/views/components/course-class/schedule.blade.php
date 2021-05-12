<div class="flex flex-col pb-8 pl-6 justify-center space-y-2 lg:pl-0 lg:space-y-0 lg:space-x-4 lg:flex-row">
    <div class="flex items-center">
        <span class="w-5 h-5 bg-green-500 rounded-md"></span>
        <span class="ml-2 text-sm">aula teórica</span>
    </div>
    <div class="flex items-center">
        <span class="w-5 h-5 bg-teal-500 rounded-md"></span>
        <span class="ml-2 text-sm">aula extra</span>
    </div>
    <div class="flex items-center">
        <span class="w-5 h-5 bg-blue-500 rounded-md"></span>
        <span class="ml-2 text-sm">aula prática</span>
    </div>
    <div class="flex items-center">
        <span class="w-5 h-5 bg-yellow-300 rounded-md"></span>
        <span class="ml-2 text-sm">folga programada</span>
    </div>
    <div class="flex items-center">
        <span class="w-5 h-5 bg-orange-500 rounded-md"></span>
        <span class="ml-2 text-sm">férias</span>
    </div>
    <div class="flex items-center">
        <span class="w-5 h-5 bg-red-500 rounded-md"></span>
        <span class="ml-2 text-sm">feriado</span>
    </div>
</div>
<div {{ $attributes }}>
    @foreach ($months as $month)
        <x-course-class.month :name="$month['name']">
            @foreach ($weeks($month) as $week)
                <tr>
                    @foreach ($week as $date)
                        @php
                            $date = $dateType($date);
                        @endphp
                        <td 
                            class="py-2 px-3 border-2 border-white rounded-md {{ $date['style'] }}"
                            datetime="{{ $dateFormat($date['date'], 'Y-m-d') }}"
                            data-type="{{ $date['type'] }}"
                            @if ($isClickable($date['type']))
                                {{ $attributes->wire('click') }}
                            @endif
                        >
                            {{ $dateFormat($date['date'], 'd') }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </x-course-class.month>
    @endforeach
</div>
