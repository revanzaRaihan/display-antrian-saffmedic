<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Display Antrian')</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/display.css') }}" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-900 overflow-x-hidden">

    <!-- HEADER -->
    <header class="fixed top-0 left-0 right-0 bg-white shadow z-50">
        <div class="max-w-screen-2xl mx-auto flex justify-between items-center px-6 py-2">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                    <span class="text-gray-500 text-sm font-semibold">LOGO</span>
                </div>
                <h1 class="text-2xl font-bold uppercase tracking-wide">SAFFMedic Display</h1>
            </div>
            <div class="text-right">
                <div id="clock" class="text-3xl font-extrabold text-green-600"></div>
                <div id="day" class="text-sm text-gray-500"></div>
            </div>
        </div>
        <div class="marquee-wrapper">
            <div id="running-text-display" class="marquee-text">{{ $marquee ?? '' }}</div>
        </div>
    </header>

    <!-- Spacer untuk header -->
    <div class="h-32"></div>

    <!-- MAIN CONTENT -->
    <main class="max-w-screen-2xl mx-auto px-6 py-2 space-y-6">
        @yield('content')
    </main>

    <!-- SCRIPT -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/display.js') }}" defer></script>
    @stack('scripts')
</body>

</html>