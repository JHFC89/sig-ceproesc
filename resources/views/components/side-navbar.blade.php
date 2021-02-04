<aside class="w-64 h-full overflow-y-auto text-gray-100 bg-gradient-to-b from-gray-800 to-gray-500">
    <div class="flex items-center h-16 px-4 text-lg uppercase bg-gray-900 shadow"> 
        <span class="font-serif text-3xl font-medium text-white">sig</span><span class="ml-2">- ceproesc</span>
    </div>
    <nav class="px-2 py-4">
        <ul class="text-gray-300 capitalize">

            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center px-2 py-2 group rounded-md {{ request()->routeIs('dashboard') ? 'font-medium text-gray-100' : '' }}">
                    <x-icons.home class="w-6 group-hover:text-gray-400"/>
                    <span class="ml-4 group-hover:text-gray-400">início</span>
                </a>
            </li>

            <li>
                <a href="#" class="flex items-center px-2 py-2 group rounded-md {{ request()->routeIs('lessons.*') ? 'font-medium text-gray-100' : '' }}">
                    <x-icons.register-lesson class="w-6 group-hover:text-gray-400"/>
                    <span class="ml-4 group-hover:text-gray-400">aulas</span>
                </a>
            </li>
            <li>
                <a href="{{ route('lessons.today') }}" class="flex items-center px-2 py-1 text-sm group rounded-md {{ request()->routeIs('lessons.today') ? 'font-medium text-gray-100' : '' }}">
                    <span class="ml-10 group-hover:text-gray-400">hoje</span>
                </a>
            </li>
            <li>
                <a href="{{ route('lessons.week') }}" class="flex items-center px-2 py-1 text-sm group rounded-md {{ request()->routeIs('lessons.week') ? 'font-medium text-gray-100' : '' }}">
                    <span class="ml-10 group-hover:text-gray-400">semana</span>
                </a>
            </li>

            @can('viewAny', \App\Models\Discipline::class)
            <li>
                <a href="{{ route('disciplines.index') }}"
                    class="
                        flex items-center px-2 py-2 group rounded-md
                        {{ request()->routeIs('disciplines.*') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <x-icons.academic-cap class="w-6 group-hover:text-gray-400"/>
                    <span class="ml-4 group-hover:text-gray-400">disciplinas</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('disciplines.index') }}"
                    class="
                        flex items-center px-2 py-1 text-sm group rounded-md
                        {{ request()->routeIs('disciplines.index') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="ml-10 group-hover:text-gray-400">todas</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('disciplines.create') }}"
                    class="
                        flex items-center px-2 py-1 text-sm group rounded-md
                        {{ request()->routeIs('disciplines.create') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="ml-10 group-hover:text-gray-400">nova</span>
                </a>
            </li>
            @endcan

        </ul>
    </nav>
</aside>
