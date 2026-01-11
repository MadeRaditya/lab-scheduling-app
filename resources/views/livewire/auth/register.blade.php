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

                {{-- Nama --}}
                <flux:input name="nama" label="Nama Lengkap" type="text" required autofocus autocomplete="name"
                    placeholder="Nama lengkap Anda" />

                {{-- NPM --}}
                <flux:input name="npm" label="NPM" type="text" required placeholder="Masukkan NPM" />

                {{-- Jurusan --}}
                <label class="text-gray-700 dark:text-white -mb-4">Jurusan</label>
                <select name="jurusan" required
                    class="w-full p-3 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="" disabled selected>Pilih jurusan</option>

                    @foreach (\App\Enums\JurusanAsisten::cases() as $jurusan)
                        <option value="{{ $jurusan->value }}">{{ $jurusan->value }}</option>
                    @endforeach
                </select>


                {{-- Email --}}
                <flux:input name="email" label="Alamat Email" type="email" required autocomplete="email"
                    placeholder="email@example.com" />

                {{-- No Telepon --}}
                <flux:input name="no_telepon" label="No Telepon" type="text" required placeholder="08xxxxxxx" />

                {{-- Password --}}
                <flux:input name="password" label="Kata Sandi" type="password" required autocomplete="new-password"
                    placeholder="Kata Sandi" viewable />

                {{-- Password Confirmation --}}
                <flux:input name="password_confirmation" label="Konfirmasi Kata Sandi" type="password" required
                    autocomplete="new-password" placeholder="Konfirmasi Kata Sandi" viewable />

                <flux:button type="submit" variant="primary"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 dark:text-white dark:bg-indigo-500 dark:hover:bg-indigo-600 transition duration-200">
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
