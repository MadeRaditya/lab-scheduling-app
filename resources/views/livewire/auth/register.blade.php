<x-layouts.auth>
    <div class="flex flex-col gap-8">

        <div class="text-center space-y-2">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white">
                Buat Akun Baru
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Masukkan detail Anda di bawah untuk membuat akun
            </p>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <div
            class="bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 max-w-md mx-auto w-full">

            <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
                @csrf

                <flux:input
                    name="name"
                    label="Nama Lengkap"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Nama lengkap Anda"
                    class="focus:ring-indigo-600 dark:focus:ring-indigo-500"
                />

                <flux:input
                    name="email"
                    label="Alamat Email"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="focus:ring-indigo-600 dark:focus:ring-indigo-500"
                />

                <flux:input
                    name="password"
                    label="Kata Sandi"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Kata Sandi"
                    viewable
                    class="focus:ring-indigo-600 dark:focus:ring-indigo-500"
                />

                <flux:input
                    name="password_confirmation"
                    label="Konfirmasi Kata Sandi"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Konfirmasi Kata Sandi"
                    viewable
                    class="focus:ring-indigo-60:0 dark:focus:ring-indigo-500"
                />

                <flux:button type="submit" variant="primary"
                    class="w-full **bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600** transition duration-200"
                    data-test="register-user-button">
                    Buat Akun
                </flux:button>

            </form>
        </div>

        <div class="text-center text-sm text-gray-600 dark:text-gray-400">
            <span>Sudah punya akun?</span>
            <flux:link :href="route('login')" wire:navigate
                class="text-indigo-600 dark:text-indigo-400 hover:underline">
                Masuk
            </flux:link>
        </div>
    </div>
</x-layouts.auth>