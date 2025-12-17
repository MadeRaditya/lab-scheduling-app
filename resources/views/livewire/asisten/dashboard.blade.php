<div class="flex h-full w-full flex-1 flex-col gap-6">
    
    <div>
        <flux:heading size="xl">Halo, {{ $user->nama }}! 👋</flux:heading>
        <flux:subheading>Selamat datang di panel asisten. Berikut ringkasan aktivitasmu.</flux:subheading>
    </div>

    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        
        <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-indigo-100 rounded-lg dark:bg-indigo-500/20">
                    <flux:icon.clock class="size-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Jadwal Berikutnya</h3>
            </div>
            
            @if($nextSchedule)
                <div class="space-y-1">
                    <p class="text-lg font-bold text-zinc-900 dark:text-white truncate">
                        {{ $nextSchedule->matakuliah->nama_mk }}
                    </p>
                    <div class="flex items-center gap-2 text-sm text-zinc-500">
                        <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $nextSchedule->hari }}</span>
                        <span>•</span>
                        <span>{{ substr($nextSchedule->jam_mulai, 0, 5) }} WIB</span>
                    </div>
                    <div class="mt-2 inline-flex items-center gap-1.5 rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                        📍 {{ $nextSchedule->ruangan->nama_ruangan }}
                    </div>
                </div>
            @else
                <p class="text-sm text-zinc-500 mt-2">Belum ada jadwal yang ditugaskan.</p>
            @endif
        </div>

        <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-emerald-100 rounded-lg dark:bg-emerald-500/20">
                    <flux:icon.briefcase class="size-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Beban Tugas</h3>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $totalKelas }}</span>
                <span class="mb-1 text-sm text-zinc-500">Kelas Semester Ini</span>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-amber-100 rounded-lg dark:bg-amber-500/20">
                    <flux:icon.academic-cap class="size-6 text-amber-600 dark:text-amber-400" />
                </div>
                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Kompetensi</h3>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $totalKompetensi }}</span>
                <span class="mb-1 text-sm text-zinc-500">Skill Terdaftar</span>
            </div>
            <div class="mt-3">
                <a href="{{ route('kompetensi') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                    Kelola Kompetensi &rarr;
                </a>
            </div>
        </div>
    </div>

    <div class="relative flex-1 overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-zinc-900 dark:text-white">Ringkasan Jadwal Minggu Ini</h3>
                <a href="{{ route('jadwalSaya') }}" class="text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-300">
                    Lihat Semua
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-zinc-700 dark:text-zinc-300">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-3 font-medium">Hari & Jam</th>
                        <th class="px-6 py-3 font-medium">Mata Kuliah</th>
                        <th class="px-6 py-3 font-medium">Ruangan</th>
                        <th class="px-6 py-3 font-medium">Peran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($upcomingSchedules as $schedule)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $schedule->hari }}</div>
                            <div class="text-xs text-zinc-500">{{ substr($schedule->jam_mulai, 0, 5) }} - {{ substr($schedule->jam_selesai, 0, 5) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $schedule->matakuliah->nama_mk }}</div>
                            <div class="text-xs text-zinc-500">{{ $schedule->matakuliah->kode_mk }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <flux:badge size="sm" color="zinc">{{ $schedule->ruangan->nama_ruangan }}</flux:badge>
                        </td>
                        <td class="px-6 py-4">
                             @php
                                $role = $schedule->penugasanAsistenFinals->firstWhere('id_asisten', $user->id_asisten)?->peran ?? 'Asisten';
                            @endphp
                            <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10 dark:bg-indigo-400/10 dark:text-indigo-400 dark:ring-indigo-400/30">
                                {{ $role }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-zinc-500">
                            <flux:icon.calendar class="mx-auto size-8 text-zinc-300 mb-2" />
                            <p>Tidak ada jadwal aktif saat ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>