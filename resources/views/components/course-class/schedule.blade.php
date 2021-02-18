<div {{ $attributes }}>
    @foreach ($months as $month)
        <x-course-class.month :name="$month['name']">
            @foreach ($weeks($month) as $week)
                <tr>
                    @foreach ($week as $date)
                        <td class="py-3 px-4 border-2 border-white rounded-md {{ $dateTypeStyles($date) }}">
                            {{ $date ? $date->format('d') : '' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </x-course-class.month>
    @endforeach
</div>
