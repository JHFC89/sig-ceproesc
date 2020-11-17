<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="api-base-url" content="{{ url('api') }}" />
    <title>@yield('title') - SIG Ceproesc</title>
    <link rel="stylesheet" href="{{ mix('css/main.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="flex h-screen overflow-hidden">

    <x-side-navbar/>

    <main class="w-full h-full overflow-y-auto bg-gray-200">

        <nav class="w-full h-16 bg-white shadow"></nav>

        <div class="px-10 py-10 space-y-8">
            @yield('content')
        </div>

    </main>

    @section('scripts')
        <script type="text/javascript" src="{{ mix('js/app.js') }}" defer></script>
    @show
</body>
</html>
