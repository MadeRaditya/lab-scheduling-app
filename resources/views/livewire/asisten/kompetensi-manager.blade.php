<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2">
        <div class="p-6 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-900 dark:border-zinc-700 space-y-6">

            <div>
                <flux:heading size="lg">Daftar Kompetensi</flux:heading>
                <flux:subheading>Kelola data keahlian dan sertifikasi kamu di sini.</flux:subheading>
            </div>

            <div class="overflow-x-auto touch-pan-x border border-zinc-200 rounded-lg dark:border-zinc-700">
                <table class="w-full text-sm text-left text-zinc-700 dark:text-zinc-300">
                    <thead
                        class="bg-zinc-50 dark:bg-zinc-800 text-zinc-500 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-4 py-3 font-medium">Nama Kompetensi</th>
                            <th class="px-4 py-3 font-medium">Tingkat</th>
                            <th class="px-4 py-3 font-medium">Tgl Sertifikasi</th>
                            <th class="px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @forelse ($kompetensis as $k)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3 font-medium whitespace-nowrap">
                                    {{ $k->nama_kompetensi }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-zinc-700 bg-zinc-100 rounded-md dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                        {{ $k->pivot->tingkat_penguasaan ?? "-"}}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($k->pivot->tanggal_sertifikasi)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <flux:button wire:click="edit({{ $k->id_kompetensi }})" icon="square-pen"
                                            size="sm" variant="subtle" aria-label="Edit" />

                                        <flux:button wire:click="delete({{ $k->id_kompetensi }})" icon="trash"
                                            size="sm" variant="danger" aria-label="Hapus" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-zinc-500">
                                    Belum ada data kompetensi.
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
                    <flux:heading size="lg">{{ $editing ? 'Edit Kompetensi' : 'Tambah Baru' }}</flux:heading>
                    <flux:subheading>Lengkapi form di bawah ini.</flux:subheading>
                </div>

                <form wire:submit.prevent="{{ $editing ? 'update' : 'add' }}" class="space-y-4">

                    <flux:select wire:model="selected_kompetensi" label="Pilih Kompetensi"
                        placeholder="Cari kompetensi...">
                        <option value="">-- Pilih --</option>
                        @foreach ($allKompetensi as $item)
                            <option value="{{ $item->id_kompetensi }}">{{ $item->nama_kompetensi }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="tingkat_penguasaan" label="Tingkat Penguasaan" placeholder="Pilih Level">
                        <option value="">-- Pilih Level --</option>
                        @foreach (\App\Enums\LevelKompetensi::cases() as $level)
                            <option value="{{ $level->value }}">{{ ucfirst($level->value) }}</option>
                        @endforeach
                    </flux:select>
                    <flux:input type="date" wire:model="tanggal_sertifikasi" label="Tanggal Sertifikasi (Optional)" />
                    <flux:input wire:model="sertifikasi" label="Nama Sertifikasi" placeholder="Nama Sertifikat (Optional)" />

                    <div class="pt-2">
                        @if ($editing)
                            <div class="flex gap-2">
                                <flux:button type="submit" variant="primary" class="w-full">Update</flux:button>
                                <flux:button type="button" wire:click="$set('editing', false)" class="w-full">Batal
                                </flux:button>
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
