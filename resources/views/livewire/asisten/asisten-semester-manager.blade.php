<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2">
        <div class="p-6 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-900 dark:border-zinc-700 space-y-6">
            
            <div>
                <flux:heading size="lg">Riwayat Kepengurusan</flux:heading>
                <flux:subheading>Sejarah status keaktifan dan jabatan per semester.</flux:subheading>
            </div>

            <div class="overflow-hidden border border-zinc-200 rounded-lg dark:border-zinc-700">
                <table class="w-full text-sm text-left text-zinc-700 dark:text-zinc-300">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-500 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-4 py-3 font-medium">Semester</th>
                            <th class="px-4 py-3 font-medium">Peran & Status</th>
                            <th class="px-4 py-3 font-medium">Periode Aktif</th>
                            <th class="px-4 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @forelse ($histories as $h)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3 font-medium whitespace-nowrap">
                                    {{ $h->semester->nama_semester ?? '-' }}
                                    @if($h->keterangan)
                                        <div class="text-xs text-zinc-400 font-normal truncate max-w-[120px]">{{ $h->keterangan }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1 items-start">
                                        @php
                                            $peranColor = match($h->peran) {
                                                'Koordinator' => 'indigo',
                                                'Supervisor'  => 'purple',
                                                default       => 'zinc'
                                            };
                                        @endphp
                                        <flux:badge size="sm" color="{{ $peranColor }}">{{ $h->peran }}</flux:badge>

                                        @php
                                            $statusColor = match($h->status) {
                                                'Aktif'     => 'green',
                                                'Cuti'      => 'yellow',
                                                'Non Aktif' => 'red',
                                                default     => 'zinc'
                                            };
                                        @endphp
                                        <div class="text-xs flex items-center gap-1">
                                            <div class="w-2 h-2 rounded-full bg-{{ $statusColor }}-500"></div>
                                            <span class="text-zinc-500">{{ $h->status }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="text-zinc-900 dark:text-zinc-100">
                                        {{ \Carbon\Carbon::parse($h->tanggal_mulai)->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-zinc-500">
                                        s/d 
                                        @if($h->tanggal_keluar)
                                            {{ \Carbon\Carbon::parse($h->tanggal_keluar)->format('d M Y') }}
                                        @else
                                            <span class="text-emerald-600 font-medium">Sekarang</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex gap-2">
                                        <flux:button wire:click="edit({{ $h->id_asisten_semester }})" icon="square-pen" size="sm" variant="subtle" />
                                        <flux:button wire:click="delete({{ $h->id_asisten_semester }})" icon="trash" size="sm" variant="danger" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-zinc-500">
                                    Belum ada riwayat kepengurusan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="sticky top-6">
            <div class="p-6 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-900 dark:border-zinc-700 space-y-6">
                
                <div>
                    <flux:heading size="lg">{{ $editing ? 'Edit Riwayat' : 'Catat Semester' }}</flux:heading>
                    <flux:subheading>Update peran dan status semesteran.</flux:subheading>
                </div>

                <form wire:submit.prevent="{{ $editing ? 'update' : 'add' }}" class="space-y-4">
                    
                    <flux:select wire:model="id_semester" label="Semester">
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id_semester }}">{{ $sem->nama_semester }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="peran" label="Peran / Jabatan">
                        @foreach (\App\Enums\AsistenPeran::cases() as $p)
                            <option value="{{ $p->value }}">{{ $p->value }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="status" label="Status Keaktifan">
                        @foreach (\App\Enums\AsistenStatus::cases() as $s)
                            <option value="{{ $s->value }}">{{ $s->value }}</option>
                        @endforeach
                    </flux:select>

                    <flux:separator />

                    <flux:input type="date" wire:model="tanggal_mulai" label="Tanggal Mulai" />

                    <flux:input type="date" wire:model="tanggal_keluar" label="Tanggal Selesai (Opsional)" />
                    <flux:description class="text-xs">Kosongkan jika masih aktif sampai sekarang.</flux:description>

                    <flux:textarea wire:model="keterangan" label="Keterangan" placeholder="Catatan tambahan (Opsional)..." />

                    <div class="pt-2">
                        @if ($editing)
                            <div class="flex gap-2">
                                <flux:button type="submit" variant="primary" class="w-full">Update</flux:button>
                                <flux:button type="button" wire:click="resetForm" class="w-full">Batal</flux:button> 
                            </div>
                        @else
                            <flux:button type="submit" variant="primary" class="w-full">Simpan</flux:button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>