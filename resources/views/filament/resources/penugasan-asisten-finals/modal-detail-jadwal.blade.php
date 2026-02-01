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
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Kelas Diampu</p>
                    <h3 class="text-2xl font-semibold">
                        {{ $asisten->penugasanAsistenFinals->count() }}
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">Kelas</span>
                    </h3>
                </div>
            </div>

            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 flex items-center gap-4">
                <div class="p-3 rounded-lg bg-emerald-50 text-emerald-600 ring-1 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-400/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata Kecocokan</p>
                    <h3 class="text-2xl font-semibold">
                        {{ number_format($asisten->penugasanAsistenFinals->avg('skor_kecocokan'), 1) }}
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">/ 100</span>
                    </h3>
                </div>
            </div>

        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden dark:border-gray-800 dark:bg-gray-900">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 dark:bg-gray-800/50 dark:border-gray-800">
                    <tr class="text-left font-medium text-gray-700 dark:text-gray-300">
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Mata Kuliah & Kelas</th>
                        <th class="px-6 py-3">Ruangan</th>
                        <th class="px-6 py-3 text-center">Skor</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach ($asisten->penugasanAsistenFinals as $tugas)
                        @php $jadwal = $tugas->jadwalFinal; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">

                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold">{{ $jadwal->hari }}</span>
                                    <span class="mt-1 w-fit rounded-md border border-gray-200 bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                        {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5">
                                    <span class="font-semibold line-clamp-1">{{ $jadwal->matakuliah->nama_mk }}</span>
                                    <div class="flex gap-2">
                                        <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700 ring-1 ring-primary-700/10 dark:bg-primary-500/10 dark:text-primary-400 dark:ring-primary-400/30">
                                            {{ $jadwal->matakuliah->kode_mk }}
                                        </span>
                                        <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-400/30">
                                            Kelas {{ $jadwal->matakuliah->kelas }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    {{ $jadwal->ruangan->nama_ruangan }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @php
                                    $score = $tugas->skor_kecocokan;
                                    if ($score >= 80) {
                                        $color = 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-400/30';
                                    } elseif ($score >= 50) {
                                        $color = 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-400/30';
                                    } else {
                                        $color = 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-400/30';
                                    }
                                @endphp
                                <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-xs font-bold ring-1 ring-inset {{ $color }}">
                                    {{ $score }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ \App\Filament\Resources\JadwalFinals\JadwalFinalResource::getUrl('edit', ['record' => $jadwal->id_final]) }}" target="_blank"
                                   class="inline-flex size-8 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                </a>
                            </td>

                        </tr>
                    @endforeach

                    @if ($asisten->penugasanAsistenFinals->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <p>Belum ada jadwal yang ditugaskan.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

    </div>
</div>
