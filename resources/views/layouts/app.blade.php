<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Aplikasi Lab Darmajaya')</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @include('partials.theme-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    @livewireStyles
</head>

<body class="relative font-sans antialiased bg-white dark:bg-gray-900 h-full">

    <livewire:navbar />
    <div id="app" class="min-h-full">

        <main>
            @yield('content')
            {{-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            </div> --}}
        </main>
    </div>

    <livewire:footer />
    @livewireScripts

    @stack('scripts')
</body>

</html>
