<header class="sticky top-0 z-50 w-full bg-white dark:bg-gray-900 shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <img class="block h-8 w-auto" src="{{ asset('favicon/android-chrome-192x192.png') }}" alt="Logo">
                            <a href="{{ url('/#hero') }}" class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Lab Darmajaya</a>
                        </div>

                        <div class="hidden lg:ml-8 lg:flex lg:space-x-8">
                            <a href="{{ url('/#hero') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-indigo-500 text-sm font-medium leading-5 text-gray-900 dark:text-white transition duration-150 ease-in-out">Home</a>
                            <a href="{{ url('/#about') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 ease-in-out">About</a>
                            <a href="{{ url('/#services') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 ease-in-out">Service</a>
                            <a href="{{ url('/#team') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 ease-in-out">Team</a>
                            <a href="{{ url('/#contact') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 ease-in-out">Contact</a>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="hidden lg:flex">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition duration-150 ease-in-out">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition duration-150 ease-in-out mr-2">Log in</a>

                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition duration-150 ease-in-out">Register</a>
                                    @endif
                                @endauth
                            @endif
                        </div>

                        <div class="mr-4">
                            @livewire('theme-toggle')
                        </div>

                        <div class="flex items-center lg:hidden">
                            <button type="button" wire:click="toggleMenu" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 transition duration-150 ease-in-out" aria-expanded="{{ $isMenuOpen ? 'true' : 'false' }}">
                                <span class="sr-only">Open main menu</span>

                                <svg class="h-6 w-6 {{ $isMenuOpen ? 'hidden' : 'block' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>

                                <svg class="h-6 w-6 {{ $isMenuOpen ? 'block' : 'hidden' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if ($isMenuOpen)
                <div class="lg:hidden" wire:click.outside="closeMenu">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="{{ url('/#hero') }}" class="block pl-3 pr-4 py-2 border-l-4 border-indigo-500 text-base font-medium text-indigo-700 bg-indigo-50 dark:bg-gray-800 dark:text-indigo-400">Home</a>
                        <a href="{{ url('/#about') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">About</a>
                        <a href="{{ url('/#services') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Service</a>
                        <a href="{{ url('/#team') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Team</a>
                        <a href="{{ url('/#contact') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Contact</a>

                        @if (Route::has('login'))
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 pb-2">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="block w-full pl-3 pr-4 py-2 text-base font-medium text-white bg-indigo-600 dark:bg-indigo-500">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Log in</a>

                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-white bg-indigo-600 dark:bg-indigo-500 mt-1">Register</a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </nav>
    </div>
</header>
