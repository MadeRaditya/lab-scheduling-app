<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    @include('partials.head')

    <script>
        (function() {
            const html = document.documentElement;
            const savedTheme = localStorage.getItem('theme');

            if (savedTheme === 'dark') {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        })();
    </script>

</head>

<body class="min-h-screen bg-white dark:bg-gray-900">
    <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-6"> <a href="{{ route('home') }}"
                class="flex flex-col items-center gap-2 font-medium transition hover:opacity-80" wire:navigate>
                <img src="{{ asset('favicon/android-chrome-192x192.png') }}" alt="{{ config('app.name') }}"
                    class="h-20 w-auto object-contain" />
                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
            </a>

            <div class="flex flex-col gap-6">
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts

    <script>
        window.localStorage.setItem("flux.appearance", "manual");

        (function() {
            const html = document.documentElement;
            const savedTheme = localStorage.getItem("theme") || "light";

            if (savedTheme === "dark") {
                html.classList.add("dark");
            } else {
                html.classList.remove("dark");
            }
        })();
    </script>

</body>

</html>
