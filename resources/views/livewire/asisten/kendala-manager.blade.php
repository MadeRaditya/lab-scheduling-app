<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- BAGIAN KIRI: DAFTAR KENDALA (2 Kolom) --}}
    <div class="lg:col-span-2">
        <div class="p-6 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-900 dark:border-zinc-700 space-y-6">
            
            <div>
                <flux:heading size="lg">Kendala & Perizinan</flux:heading>
                <flux:subheading>Ajukan izin sakit, cuti, atau kendala jadwal lainnya.</flux:subheading>
            </div>

            <div class="overflow-hidden border border-zinc-200 rounded-lg dark:border-zinc-700">
                <table class="w-full text-sm text-left text-zinc-700 dark:text-zinc-300">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-500 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-4 py-3 font-medium">Tipe / Tanggal</th>
                            <th class="px-4 py-3 font-medium">Alasan</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @forelse ($kendalas as $k)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $k->tipe_kendala }}</div>
                                    <div class="text-xs text-zinc-500">
                                        {{ \Carbon\Carbon::parse($k->tanggal_mulai)->format('d M') }} 
                                        - 
                                        {{ \Carbon\Carbon::parse($k->tanggal_selesai)->format('d M Y') }}
                                        @if($k->hari) <span class="ml-1 text-zinc-400">({{ $k->hari }})</span> @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="truncate max-w-[200px]" title="{{ $k->alasan }}">
                                        {{ $k->alasan }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $color = match($k->status) {
                                            'Disetujui' => 'green',
                                            'Ditolak'   => 'red',
                                            default     => 'yellow' // Pending
                                        };
                                        $icon = match($k->status) {
                                            'Disetujui' => 'check-circle',
                                            'Ditolak'   => 'x-circle',
                                            default     => 'clock'
                                        };
                                    @endphp
                                    <flux:badge size="sm" color="{{ $color }}" icon="{{ $icon }}">
                                        {{ $k->status }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{-- Tombol Edit/Hapus hanya muncul jika status masih Pending --}}
                                    @if($k->status === 'Pending')
                                        <div class="inline-flex gap-2">
                                            <flux:button wire:click="edit({{ $k->id_kendala }})" icon="square-pen" size="sm" variant="subtle" />
                                            <flux:button wire:click="delete({{ $k->id_kendala }})" icon="trash" size="sm" variant="danger" />
                                        </div>
                                    @else
                                        <span class="text-xs text-zinc-400 italic">Locked</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-zinc-500">
                                    Tidak ada data kendala.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- BAGIAN KANAN: FORM INPUT (1 Kolom) --}}
    <div class="lg:col-span-1">
        <div class="sticky top-6">
            <div class="p-6 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-900 dark:border-zinc-700 space-y-6">
                
                <div>
                    <flux:heading size="lg">{{ $editing ? 'Edit Kendala' : 'Ajukan Kendala' }}</flux:heading>
                    <flux:subheading>Status pengajuan baru otomatis 'Pending'.</flux:subheading>
                </div>

                <form wire:submit.prevent="{{ $editing ? 'update' : 'add' }}" class="space-y-4">
                    
                    {{-- Select Semester --}}
                    <flux:select wire:model="id_semester" label="Semester">
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id_semester }}">{{ $sem->nama_semester }}</option>
                        @endforeach
                    </flux:select>

                    {{-- Select Tipe Kendala --}}
                    <flux:select wire:model="tipe_kendala" label="Jenis Kendala">
                        @foreach (\App\Enums\KendalaTipe::cases() as $tipe)
                            <option value="{{ $tipe->value }}">{{ $tipe->value }}</option>
                        @endforeach
                    </flux:select>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-2 gap-3">
                        <flux:input type="date" wire:model="tanggal_mulai" label="Mulai" />
                        <flux:input type="date" wire:model="tanggal_selesai" label="Selesai" />
                    </div>

                    {{-- Optional Hari --}}
                    <flux:select wire:model="hari" label="Hari Spesifik (Opsional)" placeholder="-- Berlaku Semua Hari --">
                        <option value="">-- Berlaku Rentang Tanggal --</option>
                        @foreach ($hari_options as $day)
                            <option value="{{ $day }}">{{ $day }}</option>
                        @endforeach
                    </flux:select>
                    <flux:description class="text-xs">Pilih hari jika kendala hanya terjadi di hari tertentu (misal: Mata Kuliah Tiap Senin).</flux:description>

                    {{-- Alasan --}}
                    <flux:textarea wire:model="alasan" label="Alasan / Keterangan" rows="3" placeholder="Jelaskan alasan kendala..." />

                    <div class="pt-2">
                        @if ($editing)
                            <div class="flex gap-2">
                                <flux:button type="submit" variant="primary" class="w-full">Update</flux:button>
                                <flux:button type="button" wire:click="resetForm" class="w-full">Batal</flux:button> 
                            </div>
                        @else
                            <flux:button type="submit" variant="primary" class="w-full">Ajukan</flux:button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>