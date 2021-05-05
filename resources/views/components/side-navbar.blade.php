<aside class="w-64 h-full overflow-y-auto text-gray-100 bg-gradient-to-b from-gray-800 to-gray-500">
    <div class="flex items-center h-16 px-4 text-lg uppercase bg-gray-900 shadow"> 
        <span class="font-serif text-3xl font-medium text-white">sig</span><span class="ml-2">- ceproesc</span>
    </div>
    <nav class="px-2 py-4">
        <ul class="text-gray-300 capitalize">

            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center px-2 py-2 group rounded-md {{ request()->routeIs('dashboard')
|| request()->routeIs('profiles.*') 
? 'font-medium text-gray-100' : '' }}">
                    <x-icons.home class="w-6 group-hover:text-gray-400"/>
                    <span class="ml-4 group-hover:text-gray-400">início</span>
                </a>
            </li>
            <li>
                <a href="{{ route('profiles.show', ['user' => Auth::user() ]) }}" class="flex items-center px-2 py-1 text-sm group rounded-md {{ request()->routeIs('profiles.*') ? 'font-medium text-gray-100' : '' }}">
                    <span class="ml-10 group-hover:text-gray-400">conta</span>
                </a>
            </li>
            @if (Auth::user()->isEmployer())
            <li>
                <a href="{{ route('companies.novices.index', ['company' => Auth::user()->registration->company ]) }}" class="flex items-center px-2 py-1 text-sm group rounded-md {{ request()->routeIs('companies.novices.index') || request()->routeIs('novices.*') ? 'font-medium text-gray-100' : '' }}">
                    <span class="ml-10 group-hover:text-gray-400">aprendizes</span>
                </a>
            </li>
            <li>
                <a href="{{ route('companies.show', ['company' => Auth::user()->registration->company ]) }}" class="flex items-center px-2 py-1 text-sm group rounded-md {{ request()->routeIs('companies.show') ? 'font-medium text-gray-100' : '' }}">
                    <span class="ml-10 group-hover:text-gray-400">empresa</span>
                </a>
            </li>
            @endif

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

            @can('create', \App\Models\Course::class)
            <li>
                <a href="{{ route('courses.index') }}"
                    class="
                        flex items-center px-2 py-2 group rounded-md
                        {{ request()->routeIs('courses.*') || request()->routeIs('holidays.*') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <x-icons.document-text class="w-6 group-hover:text-gray-400"/>
                    <span class="ml-4 group-hover:text-gray-400">Programas</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('courses.index') }}"
                    class="
                        flex items-center px-2 py-1 text-sm group rounded-md
                        {{ request()->routeIs('courses.index') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="ml-10 group-hover:text-gray-400">todos</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('courses.create') }}"
                    class="
                        flex items-center px-2 py-1 text-sm group rounded-md
                        {{ request()->routeIs('courses.create') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="ml-10 group-hover:text-gray-400">novo</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('holidays.index') }}"
                    class="
                        flex items-center px-2 py-1 text-sm group rounded-md
                        {{ request()->routeIs('holidays.index') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="ml-10 group-hover:text-gray-400">feriados</span>
                </a>
            </li>
            @endcan

            @can('create', \App\Models\CourseClass::class)
            <li>
                <a href="{{ route('classes.index') }}"
                    class="
                        flex items-center px-2 py-2 group rounded-md
                        {{ request()->routeIs('classes.*') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <x-icons.user-group class="w-6 group-hover:text-gray-400"/>
                    <span class="ml-4 group-hover:text-gray-400">Turmas</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('classes.index') }}"
                    class="
                        flex items-center px-2 py-1 text-sm group rounded-md
                        {{ request()->routeIs('classes.index') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="ml-10 group-hover:text-gray-400">todas</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('classes.create') }}"
                    class="
                        flex items-center px-2 py-1 text-sm group rounded-md
                        {{ request()->routeIs('classes.create') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="ml-10 group-hover:text-gray-400">nova</span>
                </a>
            </li>
            @endcan

            @if (Auth::user()->isCoordinator() || Auth::user()->isAdmin())
            <li>
                <span 
                    class="
                        flex items-center px-2 py-2 group rounded-md cursor-default
                        {{ request()->routeIs('companies.*')
                        || request()->routeIs('employers.*')
                        || request()->routeIs('instructors.*')
                        || request()->routeIs('coordinators.*')
                        || request()->routeIs('admins.*')
                        ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <x-icons.user-circle class="w-6 group-hover:text-gray-400"/>
                    <span class="ml-4 group-hover:text-gray-400">Usuários</span>
                </span>
            </li>

                @can('create', \App\Models\Company::class)
                <li>
                    <a
                        href="{{ route('companies.index') }}"
                        class="
                            flex items-center px-2 py-1 text-sm group rounded-md
                            {{ request()->routeIs('companies.index')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="ml-10 group-hover:text-gray-400">empresas</span>
                    </a>
                    <a
                        href="{{ route('companies.create') }}"
                        class="
                            flex items-center px-2 py-1 text-sm group rounded-md
                            {{ request()->routeIs('companies.create')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="ml-10 group-hover:text-gray-400">nova empresa</span>
                    </a>
                </li>
                @endcan

                @can('create', \App\Models\Registration::class)
                <li>
                    <a
                        href="{{ route('instructors.index') }}"
                        class="
                            flex items-center px-2 py-1 text-sm group rounded-md
                            {{ request()->routeIs('instructors.index')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="ml-10 group-hover:text-gray-400">instrutores</span>
                    </a>
                    <a
                        href="{{ route('instructors.create') }}"
                        class="
                            flex items-center px-2 py-1 text-sm group rounded-md
                            {{ request()->routeIs('instructors.create')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="ml-10 group-hover:text-gray-400">novo instrutor</span>
                    </a>
                </li>
                @endcan

                @if(Auth::user()->isAdmin())
                <li>
                    <a
                        href="{{ route('coordinators.index') }}"
                        class="
                            flex items-center px-2 py-1 text-sm group rounded-md
                            {{ request()->routeIs('coordinators.index')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="ml-10 group-hover:text-gray-400">coordenadores</span>
                    </a>
                    <a
                        href="{{ route('coordinators.create') }}"
                        class="
                            flex items-center px-2 py-1 text-sm group rounded-md
                            {{ request()->routeIs('coordinators.create')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="ml-10 group-hover:text-gray-400">novo coordenador</span>
                    </a>
                </li>
                @endif

                @if(Auth::user()->isAdmin())
                <li>
                    <a
                        href="{{ route('admins.index') }}"
                        class="
                            flex items-center px-2 py-1 text-sm group rounded-md
                            {{ request()->routeIs('admins.index')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="ml-10 group-hover:text-gray-400">administradores</span>
                    </a>
                    <a
                        href="{{ route('admins.create') }}"
                        class="
                            flex items-center px-2 py-1 text-sm group rounded-md
                            {{ request()->routeIs('admins.create')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="ml-10 group-hover:text-gray-400">novo administrador</span>
                    </a>
                </li>
                @endif

            @endif
        </ul>
    </nav>
</aside>
