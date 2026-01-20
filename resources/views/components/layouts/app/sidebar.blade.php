<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    @include('partials.theme-script')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <img src="{{ asset('favicon/android-chrome-192x192.png') }}" alt="{{ config('app.name') }}"
                class="h-8 w-auto object-contain" />
            <div class="ms-1 grid flex-1 text-start text-sm">
                <span class="mb-0.5 truncate leading-tight font-semibold">Lab Scheduling App</span>
            </div>
        </a>

        <div class="mb-6 px-2">
            <div
                class="flex items-center gap-3 p-3 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <div class="relative shrink-0">
                    @if(auth()->user()->foto)
                        <img src="{{ Storage::url(auth()->user()->foto) }}" alt="{{ auth()->user()->name }}"
                            class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-semibold text-sm">
                            {{ auth()->user()->initials() }}
                        </div>
                    @endif
                    <div
                        class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-zinc-800 rounded-full">
                    </div>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-medium text-sm text-zinc-900 dark:text-zinc-100 truncate">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="text-xs text-zinc-500 truncate">
                        {{ auth()->user()->npm ?? auth()->user()->email }}
                    </div>
                    <div class="mt-1 flex flex-wrap gap-1">
                        <span
                            class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-400">
                            Asisten
                        </span>
                        @if (auth()->user()->status == 'aktif')
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                Aktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <flux:navlist variant="outline">

            <flux:navlist.group :heading="__('Menu')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.item icon="badge-check" :href="route('kompetensi')"
                :current="request()->routeIs('kompetensi')" wire:navigate>
                Kompetensi Saya
            </flux:navlist.item>

            <flux:navlist.item icon="calendar" :href="route('jadwalAsisten')"
                :current="request()->routeIs('jadwalAsisten')" wire:navigate>
                Jadwal Kuliah Saya
            </flux:navlist.item>

            <flux:navlist.item icon="sliders-horizontal" :href="route('preferensiJadwalAsisten')"
                :current="request()->routeIs('preferensiJadwalAsisten')" wire:navigate>
                Preferensi Jadwal Saya
            </flux:navlist.item>

            <flux:navlist.item icon="octagon-alert" :href="route('kendalaAsisten')"
                :current="request()->routeIs('kendalaAsisten')" wire:navigate>
                Kendala Saya
            </flux:navlist.item>

            <flux:navlist.item icon="clipboard-check" :href="route('asistenSemester')"
                :current="request()->routeIs('asistenSemester')" wire:navigate>
                Asistensi Semester
            </flux:navlist.item>

            <flux:navlist.item icon="calender" :href="route('jadwalSaya')" :current="request()->routeIs('jadwalSaya')"
                wire:navigate>
                Jadwal saya
            </flux:navlist.item>

        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="folder-git-2" href="https://github.com/MadeRaditya/lab-scheduling-app"
                target="_blank">
                {{ __('Repository') }}
            </flux:navlist.item>

            <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire"
                target="_blank">
                {{ __('Documentation') }}
            </flux:navlist.item>
        </flux:navlist>

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" data-test="sidebar-menu-button" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full"
                        data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
                </flux:menu.item>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full"
                        data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
