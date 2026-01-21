<div class="space-y-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <flux:heading size="lg">Jadwal Asisten</flux:heading>
            <flux:subheading>Kelola jadwal ketersediaan atau kegiatan kamu.</flux:subheading>
        </div>

        <div class="flex bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <button wire:click="switchMode('list')"
                class="px-4 py-2 text-sm font-medium rounded-md transition {{ $mode === 'list' ? 'bg-white shadow text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                List Jadwal
            </button>
            <button wire:click="switchMode('bulk')"
                class="px-4 py-2 text-sm font-medium rounded-md transition {{ $mode === 'bulk' ? 'bg-white shadow text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                + Input Paket
            </button>
            <button wire:click="switchMode('single')"
                class="px-4 py-2 text-sm font-medium rounded-md transition {{ $mode === 'single' ? 'bg-white shadow text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                + Manual
            </button>
        </div>
    </div>

    @if($mode === 'list')
        <div class="overflow-auto border border-zinc-200 rounded-lg dark:border-zinc-700">
            <table class="w-full text-sm text-left text-zinc-700 dark:text-zinc-300">
                <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-500 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Hari / Jam</th>
                        <th class="px-4 py-3 font-medium">Kegiatan</th>
                        <th class="px-4 py-3 font-medium">Semester</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                    @forelse ($jadwals as $jadwal)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $jadwal->hari }}</div>
                                <div class="text-xs text-zinc-500">
                                    {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $jadwal->jenis_kegiatan }}</div>
                                @if($jadwal->keterangan)
                                    <div class="text-xs text-zinc-500 truncate max-w-[150px]">{{ $jadwal->keterangan }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-zinc-100 rounded-md dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                                    {{ $jadwal->semester->nama_semester ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex gap-2">
                                    <flux:button wire:click="edit({{ $jadwal->id_jadwal_asisten }})" icon="square-pen" size="sm" variant="subtle" />
                                    <flux:button wire:click="delete({{ $jadwal->id_jadwal_asisten }})" icon="trash" size="sm" variant="danger" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-zinc-500">
                                Belum ada jadwal. Silakan tambah data.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($mode === 'bulk')
        <div class="p-6 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-900 dark:border-zinc-700">
            <flux:heading size="lg" class="mb-4">Generator Jadwal Paket</flux:heading>

            <form wire:submit.prevent="saveBulk" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="id_semester" label="Semester" placeholder="Pilih Semester...">
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id_semester }}">{{ $sem->nama_semester }}</option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="jenis_kegiatan" label="Jenis Kegiatan" placeholder="Contoh: Piket, Mengajar" />

                    <flux:input wire:model="keterangan" label="Keterangan (Opsional)" placeholder="Catatan tambahan..." />
                </div>

                <flux:separator text="Pilih Slot Waktu" />

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($this->standardSlots as $hari => $slots)
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3">
                            <h4 class="font-bold text-zinc-900 dark:text-zinc-100 mb-3 border-b border-zinc-100 pb-2">{{ $hari }}</h4>

                            <div class="flex flex-wrap gap-2">
                                @foreach($slots as $slot)
                                    @php
                                        $value = $hari . '|' . $slot[0] . '|' . $slot[1];
                                    @endphp
                                    <label class="cursor-pointer select-none">
                                        <input type="checkbox" wire:model="bulk_selected_slots" value="{{ $value }}" class="peer sr-only">
                                        <div class="px-2 py-1 text-xs font-medium border rounded transition-colors
                                            text-zinc-600 border-zinc-200 bg-zinc-50
                                            peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600
                                            dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-400
                                            dark:peer-checked:bg-indigo-500 dark:peer-checked:text-white">
                                            {{ $slot[0] }} - {{ $slot[1] }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end pt-4 gap-2">
                    <flux:button type="button" wire:click="switchMode('list')" variant="subtle">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan Semua yang Dipilih</flux:button>
                </div>
            </form>
        </div>
    @endif

    @if($mode === 'single')
        <div class="p-6 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-900 dark:border-zinc-700 max-w-2xl mx-auto">
            <flux:heading size="lg" class="mb-4">{{ $editing ? 'Edit Jadwal' : 'Tambah Jadwal Custom' }}</flux:heading>

            <form wire:submit.prevent="{{ $editing ? 'update' : 'add' }}" class="space-y-4">
                 <flux:select wire:model="id_semester" label="Semester">
                    @foreach ($semesters as $sem)
                        <option value="{{ $sem->id_semester }}">{{ $sem->nama_semester }}</option>
                    @endforeach
                 </flux:select>

                 <flux:select wire:model="hari" label="Hari">
                    <option value="">-- Pilih --</option>
                    @foreach ($hari_options as $day)
                        <option value="{{ $day }}">{{ $day }}</option>
                    @endforeach
                 </flux:select>

                 <div class="grid grid-cols-2 gap-3">
                    <flux:input type="time" wire:model="jam_mulai" label="Mulai" />
                    <flux:input type="time" wire:model="jam_selesai" label="Selesai" />
                 </div>

                 <flux:input wire:model="jenis_kegiatan" label="Jenis Kegiatan" />
                 <flux:textarea wire:model="keterangan" label="Keterangan" />

                 <div class="flex gap-2 pt-2">
                    <flux:button type="submit" variant="primary" class="w-full">{{ $editing ? 'Update' : 'Simpan' }}</flux:button>
                    <flux:button type="button" wire:click="switchMode('list')" class="w-full">Batal</flux:button>
                 </div>
            </form>
        </div>
    @endif

</div>
