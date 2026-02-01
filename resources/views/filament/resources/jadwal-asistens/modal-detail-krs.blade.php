@vite(['resources/css/app.css'])

<div class="antialiased font-sans text-gray-900 dark:text-gray-100">
    <div class="flex flex-col gap-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 flex items-center gap-4">
                <div class="p-3 rounded-lg bg-primary-50 text-primary-600 ring-1 ring-primary-600/10 dark:bg-primary-500/10 dark:text-primary-400 dark:ring-primary-400/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Kegiatan</p>
                    <h3 class="text-2xl font-semibold">
                        {{ $asisten->jadwalAsistens->count() }}
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">Sesi</span>
                    </h3>
                </div>
            </div>

            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 flex items-center gap-4">
                @php
                    $total = $asisten->jadwalAsistens->count();
                    if ($total > 15) {
                        $status = 'Sangat Sibuk';
                        $color = 'bg-red-50 text-red-600 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-400/30';
                        $icon = 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z';
                    } elseif ($total > 10) {
                        $status = 'Cukup Sibuk';
                        $color = 'bg-amber-50 text-amber-600 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-400/30';
                        $icon = 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z';
                    } else {
                        $status = 'Tersedia';
                        $color = 'bg-emerald-50 text-emerald-600 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-400/30';
                        $icon = 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                    }
                @endphp
                <div class="p-3 rounded-lg ring-1 {{ $color }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Ketersediaan</p>
                    <h3 class="text-xl font-semibold">{{ $status }}</h3>
                </div>
            </div>

        </div>

        @php
            $grouped = $asisten->jadwalAsistens
                ->sortBy('jam_mulai')
                ->groupBy('hari');
        @endphp

        @if ($grouped->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-700">
                <p class="text-gray-500 dark:text-gray-400">Asisten belum mengisi jadwal KRS/Kesibukan.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($grouped as $hari => $items)
                    <div class="flex flex-col rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-800/50">
                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $hari }}</h4>
                        </div>

                        <div class="p-4 flex flex-col gap-2">
                            @foreach ($items as $jadwal)
                                <div class="flex items-center justify-between rounded-lg border border-gray-100 p-2 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/40 transition-colors">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $jadwal->keterangan ?? '-' }}
                                        </span>
                                    </div>

                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset
                                        {{ $jadwal->jenis_kegiatan === 'Kuliah'
                                            ? 'bg-primary-50 text-primary-700 ring-primary-700/10 dark:bg-primary-500/10 dark:text-primary-400 dark:ring-primary-400/30'
                                            : 'bg-purple-50 text-purple-700 ring-purple-700/10 dark:bg-purple-500/10 dark:text-purple-400 dark:ring-purple-400/30' }}">
                                        {{ $jadwal->jenis_kegiatan === 'Kuliah' ? 'Perkuliahan' : 'Pribadi' }}
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
