@vite(['resources/css/app.css'])

<div class="antialiased font-sans text-gray-900 dark:text-gray-100">
    <div class="flex flex-col gap-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 flex items-center gap-4">
                <div class="p-3 rounded-lg bg-primary-50 text-primary-600 ring-1 ring-primary-600/10 dark:bg-primary-500/10 dark:text-primary-400 dark:ring-primary-400/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Slot Waktu</p>
                    <h3 class="text-2xl font-semibold">
                        {{ $asisten->preferensiJadwalAsistens->count() }}
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">Sesi</span>
                    </h3>
                </div>
            </div>

            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 flex items-center gap-4">
                @php
                    $prioritasCount = $asisten->preferensiJadwalAsistens
                        ->where('tingkat_preferensi', \App\Enums\PreferensiTingkat::PrioritasUtama->value)
                        ->count();
                @endphp
                <div class="p-3 rounded-lg bg-emerald-50 text-emerald-600 ring-1 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-400/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Slot Prioritas Utama</p>
                    <h3 class="text-2xl font-semibold">
                        {{ $prioritasCount }}
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">Sesi</span>
                    </h3>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 gap-4">
            @php
                $grouped = $asisten->preferensiJadwalAsistens
                    ->sortBy('jam_mulai')
                    ->groupBy('hari')
                    ->sortBy(function ($items, $key) {
                        $days = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
                        return $days[$key] ?? 99;
                    });
            @endphp

            @if ($grouped->isEmpty())
                <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-700">
                    <p class="text-gray-500 dark:text-gray-400">Belum ada preferensi jadwal yang diinput.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($grouped as $hari => $slots)
                        <div class="flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-800/50">
                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $hari }}</h4>
                            </div>

                            <div class="flex flex-col gap-2 p-4 flex-grow">
                                @foreach ($slots as $slot)
                                    @php
                                        $tingkat = $slot->tingkat_preferensi;
                                        if ($tingkat === \App\Enums\PreferensiTingkat::PrioritasUtama->value) {
                                            $badgeClass = 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-400/30';
                                        } elseif ($tingkat === \App\Enums\PreferensiTingkat::Bersedia->value) {
                                            $badgeClass = 'bg-primary-50 text-primary-700 ring-primary-700/10 dark:bg-primary-500/10 dark:text-primary-400 dark:ring-primary-400/30';
                                        } elseif ($tingkat === \App\Enums\PreferensiTingkat::Cadangan->value) {
                                            $badgeClass = 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-400/30';
                                        } else {
                                            $badgeClass = 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-400/30';
                                        }
                                    @endphp

                                    <div class="flex items-center justify-between rounded-lg border border-gray-100 p-2 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/40 transition-colors">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ substr($slot->jam_mulai, 0, 5) }} - {{ substr($slot->jam_selesai, 0, 5) }}
                                        </span>
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $badgeClass }}">
                                            {{ $tingkat }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
