<x-layouts.auth>
    <div class="flex flex-col gap-8">

        <div class="text-center space-y-2">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white">
                Masuk ke Akun Anda
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Masukkan email dan kata sandi Anda di bawah untuk masuk
            </p>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <div
            class="bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 max-w-md mx-auto w-full">

            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
                @csrf

                <flux:input name="email" label="Alamat Email" type="email" required autofocus autocomplete="email"
                    placeholder="email@example.com" class="focus:ring-indigo-600 dark:focus:ring-indigo-500" />

                <div class="relative">
                    <flux:input name="password" label="Kata Sandi" type="password" required
                        autocomplete="current-password" placeholder="Kata Sandi" viewable
                        class="focus:ring-indigo-600 dark:focus:ring-indigo-500" />

                    @if (Route::has('password.request'))
                        <flux:link
                            class="absolute top-0 end-0 text-sm **text-indigo-600 dark:text-indigo-400 hover:underline**"
                            :href="route('password.request')" wire:navigate>
                            Lupa kata sandi?
                        </flux:link>
                    @endif
                </div>

                <flux:checkbox name="remember" label="Ingat saya" :checked="old('remember')" />

                <flux:button variant="primary" type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 dark:text-white dark:bg-indigo-500 dark:hover:bg-indigo-600 transition duration-200">
                    Masuk
                </flux:button>

                <flux:button variant="filled" as="a" href="{{ route('filament.admin.auth.login') }}"
                    wire:navigate
                    class="w-full bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 text-white transition duration-200">
                    Masuk sebagai Admin
                </flux:button>

            </form>
        </div>

        @if (Route::has('register'))
            <div class="text-sm text-center text-gray-600 dark:text-gray-400">
                <span>Belum punya akun?</span>
                <flux:link :href="route('register')" wire:navigate
                    class="text-indigo-600 dark:text-indigo-400 hover:underline">
                    Daftar
                </flux:link>
            </div>
        @endif

    </div>
</x-layouts.auth>
