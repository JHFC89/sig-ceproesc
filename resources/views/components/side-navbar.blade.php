<aside {{ $attributes->merge(['class' => 'w-64 h-full overflow-y-auto text-gray-100 bg-gradient-to-b from-gray-800 to-gray-500']) }}>
    <div class="hidden flex items-center h-16 px-4 text-lg uppercase bg-gray-900 shadow lg:block"> 
        <span class="font-serif text-3xl font-medium text-white">sig</span><span class="ml-2">- ceproesc</span>
    </div>
    <nav class="px-2 py-4">
        <ul class="text-gray-300 capitalize">

            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center px-2 py-2 group border lg:border-none rounded-md {{ request()->routeIs('dashboard')
|| request()->routeIs('profiles.*') 
? 'font-medium text-gray-100' : '' }} lg:justify-start">
                    <x-icons.home class="w-6 group-hover:text-gray-400"/>
                    <span class="ml-4 group-hover:text-gray-400">início</span>
                </a>
            </li>
            <li>
                <a href="{{ route('profiles.show', ['user' => Auth::user() ]) }}" class="flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md {{ request()->routeIs('profiles.*') ? 'font-medium text-gray-100' : '' }} lg:justify-start">
                    <span class="lg:ml-10 group-hover:text-gray-400">conta</span>
                </a>
            </li>
            @if (Auth::user()->isEmployer())
            <li>
                <a href="{{ route('companies.novices.index', ['company' => Auth::user()->registration->company ]) }}" class="flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md {{ request()->routeIs('companies.novices.index') || request()->routeIs('novices.*') ? 'font-medium text-gray-100' : '' }} lg:justify-start">
                    <span class="lg:ml-10 group-hover:text-gray-400">aprendizes</span>
                </a>
            </li>
            <li>
                <a href="{{ route('companies.show', ['company' => Auth::user()->registration->company ]) }}" class="flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md {{ request()->routeIs('companies.show') ? 'font-medium text-gray-100' : '' }} lg:justify-start">
                    <span class="lg:ml-10 group-hover:text-gray-400">empresa</span>
                </a>
            </li>
            @endif

            <li>
                <a href="#" class="flex items-center justify-center px-2 py-2 group border lg:border-none rounded-md {{ request()->routeIs('lessons.*') || request()->routeIs('classes.lessons.index') ? 'font-medium text-gray-100' : '' }} lg:justify-start">
                    <x-icons.register-lesson class="w-6 group-hover:text-gray-400"/>
                    <span class="ml-4 group-hover:text-gray-400">aulas</span>
                </a>
            </li>
            <li>
                <a href="{{ route('lessons.today') }}" class="flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md {{ request()->routeIs('lessons.today') ? 'font-medium text-gray-100' : '' }} lg:justify-start">
                    <span class="lg:ml-10 group-hover:text-gray-400">hoje</span>
                </a>
            </li>
            <li>
                <a href="{{ route('lessons.week') }}" class="flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md {{ request()->routeIs('lessons.week') ? 'font-medium text-gray-100' : '' }} lg:justify-start">
                    <span class="lg:ml-10 group-hover:text-gray-400">semana</span>
                </a>
            </li>
            @if (Auth::user()->isNovice() && Auth::user()->courseClass !== null)
            <li>
                <a href="{{ route('classes.lessons.index', ['courseClass' => Auth::user()->courseClass]) }}" class="flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md {{ request()->routeIs('classes.lessons.index') ? 'font-medium text-gray-100' : '' }} lg:justify-start">
                    <span class="lg:ml-10 group-hover:text-gray-400">todas</span>
                </a>
            </li>
            <li>
                <a href="{{ route('novices.frequencies.show', ['registration' => Auth::user()->registration]) }}" class="flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md {{ request()->routeIs('novices.frequencies.show') ? 'font-medium text-gray-100' : '' }} lg:justify-start">
                    <span class="lg:ml-10 group-hover:text-gray-400">frequência</span>
                </a>
            </li>
            @endif

            @can('viewAny', \App\Models\Discipline::class)
            <li>
                <a href="{{ route('disciplines.index') }}"
                    class="
                        flex items-center justify-center px-2 py-2 group border lg:border-none rounded-md lg:justify-start
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
                        flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                        {{ request()->routeIs('disciplines.index') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="lg:ml-10 group-hover:text-gray-400">todas</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('disciplines.create') }}"
                    class="
                        flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                        {{ request()->routeIs('disciplines.create') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="lg:ml-10 group-hover:text-gray-400">nova</span>
                </a>
            </li>
            @endcan

            @can('create', \App\Models\Course::class)
            <li>
                <a href="{{ route('courses.index') }}"
                    class="
                        flex items-center justify-center px-2 py-2 group border lg:border-none rounded-md lg:justify-start
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
                        flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                        {{ request()->routeIs('courses.index') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="lg:ml-10 group-hover:text-gray-400">todos</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('courses.create') }}"
                    class="
                        flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                        {{ request()->routeIs('courses.create') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="lg:ml-10 group-hover:text-gray-400">novo</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('holidays.index') }}"
                    class="
                        flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                        {{ request()->routeIs('holidays.index') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="lg:ml-10 group-hover:text-gray-400">feriados</span>
                </a>
            </li>
            @endcan

            @can('create', \App\Models\CourseClass::class)
            <li>
                <a href="{{ route('classes.index') }}"
                    class="
                        flex items-center justify-center px-2 py-2 group border lg:border-none rounded-md lg:justify-start
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
                        flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                        {{ request()->routeIs('classes.index') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="lg:ml-10 group-hover:text-gray-400">todas</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('classes.create') }}"
                    class="
                        flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                        {{ request()->routeIs('classes.create') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="lg:ml-10 group-hover:text-gray-400">nova</span>
                </a>
            </li>
            @endcan
            @if (Auth::user()->isNovice() && Auth::user()->courseClass !== null)
            <li>
                <a href="{{ route('classes.show', ['courseClass' => Auth::user()->courseClass]) }}"
                    class="
                        flex items-center justify-center px-2 py-2 group border lg:border-none rounded-md lg:justify-start
                        {{ request()->routeIs('classes.*') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <x-icons.user-group class="w-6 group-hover:text-gray-400"/>
                    <span class="ml-4 group-hover:text-gray-400">Turma</span>
                </a>
            </li>
            @elseif (Auth::user()->isInstructor())
            <li>
                <a href="{{ route('classes.index') }}"
                    class="
                        flex items-center justify-center px-2 py-2 group border lg:border-none rounded-md lg:justify-start
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
                        flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                        {{ request()->routeIs('classes.index') ? 'font-medium text-gray-100' : '' }}
                    "
                >
                    <span class="lg:ml-10 group-hover:text-gray-400">todas</span>
                </a>
            </li>
            @endif

            @if (Auth::user()->isCoordinator() || Auth::user()->isAdmin())
            <li>
                <span 
                    class="
                        flex items-center justify-center px-2 py-2 group border lg:border-none rounded-md cursor-default lg:justify-start
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
                            flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                            {{ request()->routeIs('companies.index')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="lg:ml-10 group-hover:text-gray-400">empresas</span>
                    </a>
                    <a
                        href="{{ route('companies.create') }}"
                        class="
                            flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                            {{ request()->routeIs('companies.create')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="lg:ml-10 group-hover:text-gray-400">nova empresa</span>
                    </a>
                </li>
                @endcan

                @can('create', \App\Models\Registration::class)
                <li>
                    <a
                        href="{{ route('instructors.index') }}"
                        class="
                            flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                            {{ request()->routeIs('instructors.index')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="lg:ml-10 group-hover:text-gray-400">instrutores</span>
                    </a>
                    <a
                        href="{{ route('instructors.create') }}"
                        class="
                            flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                            {{ request()->routeIs('instructors.create')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="lg:ml-10 group-hover:text-gray-400">novo instrutor</span>
                    </a>
                </li>
                @endcan

                @if(Auth::user()->isAdmin())
                <li>
                    <a
                        href="{{ route('coordinators.index') }}"
                        class="
                            flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                            {{ request()->routeIs('coordinators.index')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="lg:ml-10 group-hover:text-gray-400">coordenadores</span>
                    </a>
                    <a
                        href="{{ route('coordinators.create') }}"
                        class="
                            flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                            {{ request()->routeIs('coordinators.create')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="lg:ml-10 group-hover:text-gray-400">novo coordenador</span>
                    </a>
                </li>
                @endif

                @if(Auth::user()->isAdmin())
                <li>
                    <a
                        href="{{ route('admins.index') }}"
                        class="
                            flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                            {{ request()->routeIs('admins.index')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="lg:ml-10 group-hover:text-gray-400">administradores</span>
                    </a>
                    <a
                        href="{{ route('admins.create') }}"
                        class="
                            flex items-center justify-center px-2 py-2 lg:py-1 lg:text-sm group rounded-md lg:justify-start
                            {{ request()->routeIs('admins.create')
                            ? 'font-medium text-gray-100' : '' }}
                        "
                    >
                        <span class="lg:ml-10 group-hover:text-gray-400">novo administrador</span>
                    </a>
                </li>
                @endif

            @endif
        </ul>
    </nav>
</aside>
