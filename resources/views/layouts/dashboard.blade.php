<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="api-base-url" content="{{ url('api') }}" />
    <meta name="api_token" content="Bearer {{ Auth::user()->api_token }}">

    <title>@yield('title') - SIG Ceproesc</title>

    <link rel="stylesheet" href="{{ mix('css/main.css') }}">

    @stack('head')
</head>
<body class="flex h-screen overflow-hidden antialiased">

    <x-side-navbar/>

    <div class="w-full h-full overflow-y-auto bg-gray-200">

        @include('layouts.navigation')

        <main class="w-full">

            <div class="px-10 py-10 space-y-8">
                @yield('content')
            </div>

        </main>
    </div>

    @stack('footer')
    @section('scripts')
        <script type="text/javascript" src="{{ mix('js/app.js') }}" defer></script>
    @show
</body>
</html>
