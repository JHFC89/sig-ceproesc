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
