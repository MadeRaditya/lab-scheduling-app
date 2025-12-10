<div>
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Jadwal Praktikum Saya</flux:heading>
            <flux:subheading>Daftar jadwal final dimana kamu ditugaskan.</flux:subheading>
        </div>

        <div class="p-6 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-900 dark:border-zinc-700">
            <div class="overflow-hidden border border-zinc-200 rounded-lg dark:border-zinc-700">
                <table class="w-full text-sm text-left text-zinc-700 dark:text-zinc-300">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-500 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-4 py-3 font-medium">Semester</th>
                            <th class="px-4 py-3 font-medium">Mata Kuliah</th>
                            <th class="px-4 py-3 font-medium">Waktu & Ruangan</th>
                            <th class="px-4 py-3 font-medium">Peran Saya</th>
                            <th class="px-4 py-3 font-medium">Partner Bertugas</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @forelse ($jadwals as $jadwal)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3">
                                    <flux:badge size="sm" color="zinc">{{ $jadwal->semester->nama_semester }}</flux:badge>
                                </td>

                                <td class="px-4 py-3 font-medium">
                                    <div class="text-zinc-900 dark:text-zinc-100">{{ $jadwal->matakuliah->nama_mk }}</div>
                                    <div class="text-xs text-zinc-500">{{ $jadwal->matakuliah->kode_mk }}</div>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $jadwal->hari }}</div>
                                    <div class="text-xs text-zinc-500 mb-1">
                                        {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                    </div>
                                    <div class="text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                        📍 {{ $jadwal->ruangan->nama_ruangan }}
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    @php
                                        $myAssignment = $jadwal->penugasanAsistenFinals->firstWhere('id_asisten', Auth::user()->id_asisten);
                                    @endphp
                                    @if ($myAssignment)
                                        <flux:badge size="sm" color="indigo">{{ $myAssignment->peran }}</flux:badge>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        @foreach ($jadwal->penugasanAsistenFinals as $tugas)
                                            @if ($tugas->id_asisten !== Auth::user()->id_asisten)
                                                <div class="flex items-center gap-2 text-xs">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-zinc-400"></div>
                                                    <span>{{ $tugas->asisten->nama }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                        
                                        @if ($jadwal->penugasanAsistenFinals->count() <= 1)
                                            <span class="text-xs text-zinc-400 italic">Solo (Sendiri)</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    @php
                                        $color = match ($jadwal->status) {
                                            'Aktif' => 'green',
                                            'Draft' => 'yellow',
                                            'Selesai' => 'zinc',
                                            default => 'zinc',
                                        };
                                    @endphp
                                    <flux:badge size="sm" color="{{ $color }}">{{ $jadwal->status }}</flux:badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-zinc-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <flux:icon.calendar-days class="size-10 text-zinc-300 mb-2" />
                                        <p>Belum ada jadwal final yang ditugaskan kepadamu.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>