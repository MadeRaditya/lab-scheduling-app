<div class="flex flex-col gap-8">
    <div class="text-center space-y-2">
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white">
            Buat Akun Baru
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Hanya asisten terpilih yang memiliki token yang dapat mendaftar.
        </p>
    </div>

    <div
        class="bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 max-w-md mx-auto w-full">

        @if (!$tokenValid)
            <div class="space-y-4">
                <flux:input wire:model="token_registrasi" label="Token Registrasi"
                    placeholder="Masukkan Token dari Admin" />

                <button wire:click="cekToken" type="button"
                    class="w-full py-2 px-4 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                    <span wire:loading.remove wire:target="cekToken">Cek Token</span>
                    <span wire:loading wire:target="cekToken">Memeriksa...</span>
                </button>

                @error('token_registrasi')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        @endif

        @if ($tokenValid)
            <form wire:submit="register" class="flex flex-col gap-6">

                <div class="bg-green-50 p-3 rounded text-green-700 text-sm mb-2">
                    Token Valid! Silakan lengkapi data.
                </div>

                <flux:input wire:model="nama" label="Nama Lengkap" readonly />
                <flux:input wire:model="npm" label="NPM" readonly />

                <div>
                    <label class="text-gray-700 dark:text-white mb-1 block">Jurusan</label>
                    <select wire:model="jurusan" required class="w-full p-2 border rounded dark:bg-gray-800">
                        <option value="">Pilih Jurusan</option>
                        @foreach (\App\Enums\JurusanAsisten::cases() as $jurusan)
                            <option value="{{ $jurusan->value }}">{{ $jurusan->value }}</option>
                        @endforeach
                    </select>
                    @error('jurusan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <flux:input wire:model="email" label="Alamat Email" type="email" required autofocus
                    autocomplete="email" placeholder="email@example.com"
                    class="focus:ring-indigo-600 dark:focus:ring-indigo-500" />
                <flux:input wire:model="no_telepon" label="No Telepon" required
                    class="focus:ring-indigo-600 dark:focus:ring-indigo-500" />
                <flux:input wire:model="password" label="Kata Sandi" type="password" required placeholder="Kata Sandi"
                    viewable class="focus:ring-indigo-600 dark:focus:ring-indigo-500" />
                <flux:input wire:model="password_confirmation" label="Konfirmasi Sandi" type="password" required
                    viewable class="focus:ring-indigo-600 dark:focus:ring-indigo-500" />

                <button type="submit"
                    class="w-full py-2 px-4 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                    Daftar Sekarang
                </button>
            </form>
        @endif
    </div>

    <div class="text-center text-sm text-gray-600 dark:text-gray-400">
        <span>Sudah punya akun?</span>
        <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Masuk</a>
    </div>
</div>
