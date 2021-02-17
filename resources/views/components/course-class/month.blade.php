    @foreach ($months as $month)
        <div class="{{ $attributes->merge(['class' => 'flex justify-center']) }}">
            <div class="inline-block rounded-md shadow">
                <div class="py-2 bg-gray-100 text-center font-mono text-sm font-bold tracking-wide text-gray-600 uppercase">{{ $month['name'] }}</div>
                <div class="px-2">
                    <table>
                        <thead>
                            <tr>
                                <th class="py-3 px-4">d</th>
                                <th class="py-3 px-4">s</th>
                                <th class="py-3 px-4">t</th>
                                <th class="py-3 px-4">q</th>
                                <th class="py-3 px-4">q</th>
                                <th class="py-3 px-4">s</th>
                                <th class="py-3 px-4">s</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($weeks($month) as $week)
                                <tr>
                                    @foreach ($week as $date)
                                        <td class="py-3 px-4 border-2 border-white rounded-md {{ $dateTypeStyles($date) }}">
                                            {{ $date ? $date->format('d') : '' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>
