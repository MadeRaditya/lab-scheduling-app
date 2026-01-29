<?php

namespace App\Filament\Resources\Asistens\Pages;

use App\Filament\Resources\Asistens\AsistenResource;
use App\Models\Asisten;
use App\Models\Matakuliah;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class KelolaKualifikasi extends Page implements HasSchemas, HasTable
{
    use InteractsWithSchemas;
    use InteractsWithTable;

    protected static string $resource = AsistenResource::class;
    protected static ?string $title = 'Verifikasi Kualifikasi Asisten';

    public Asisten $record;

    public function getView(): string
    {
        return 'filament.resources.asistens.pages.kelola-kualifikasi';
    }

    public function mount(Asisten $record)
    {
        $this->record = $record;
    }

    public function assistantInfolist(Schema $schema): Schema
    {
        return $schema->record($this->record)->components([
            Section::make('Kartu Identitas Asisten')
                ->columns(4)
                ->components([
                    ImageEntry::make('foto')
                        ->defaultImageUrl('https://ui-avatars.com/api/?name=' . urlencode($this->record->nama))
                        ->circular()
                        ->columnSpan(1),
                    Section::make()
                        ->columnSpan(3)
                        ->components([
                            TextEntry::make('nama')->size('lg')->weight('bold')->label('Nama Lengkap'),
                            TextEntry::make('npm')->label('NIM')->copyable(),
                            TextEntry::make('kompetensis.nama_kompetensi')
                                ->label('Kompetensi Dasar')
                                ->badge()
                                ->color('success')
                                ->listWithLineBreaks(),
                        ]),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Matakuliah::query()->fromSub(
                    Matakuliah::select('nama_mk', DB::raw('MIN(id) as id'))
                        ->groupBy('nama_mk'),
                    'matakuliahs'
                )
            )
            ->defaultSort('nama_mk', 'asc')
            ->heading('Daftar Mata Kuliah Tersedia')
            ->description('Klik tombol aksi untuk memverifikasi asisten pada SELURUH kelas mata kuliah tersebut.')
            ->columns([
                TextColumn::make('nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah_kelas')
                    ->label('Total Kelas')
                    ->state(fn (Matakuliah $mk) =>
                        Matakuliah::where('nama_mk', $mk->nama_mk)->count()
                    )
                    ->badge(),

                TextColumn::make('status_verifikasi')
                    ->label('Status')
                    ->badge()
                    ->state(function (Matakuliah $mk) {
                        $ids = Matakuliah::where('nama_mk', $mk->nama_mk)->pluck('id');

                        $count = DB::table('asisten_matakuliah')
                            ->where('id_asisten', $this->record->id_asisten)
                            ->whereIn('matakuliah_id', $ids)
                            ->count();

                        if ($count === 0) return 'Belum';
                        if ($count === $ids->count()) return 'Terverifikasi';
                        return 'Parsial';
                    })
                    ->color(fn (string $state) => match ($state) {
                        'Terverifikasi' => 'success',
                        'Parsial' => 'warning',
                        'Belum' => 'danger',
                    }),
            ])
            ->actions([
                Action::make('verifikasi')
                    ->label('Verifikasi Semua')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Kualifikasi')
                    ->modalDescription(fn (Matakuliah $mk) =>
                        "Apakah Anda yakin ingin memberikan kualifikasi untuk mata kuliah '{$mk->nama_mk}'?"
                    )
                    ->visible(function (Matakuliah $mk) {
                        $ids = Matakuliah::where('nama_mk', $mk->nama_mk)->pluck('id');

                        $count = DB::table('asisten_matakuliah')
                            ->where('id_asisten', $this->record->id_asisten)
                            ->whereIn('matakuliah_id', $ids)
                            ->count();

                        return $count < $ids->count();
                    })
                    ->action(function (Matakuliah $mk) {
                        $ids = Matakuliah::where('nama_mk', $mk->nama_mk)->pluck('id')->toArray();

                        $this->record->matakuliahs()->syncWithoutDetaching($ids);

                        Notification::make()
                            ->title('Berhasil Diverifikasi')
                            ->body('Asisten kini terverifikasi untuk ' . count($ids) . " kelas {$mk->nama_mk}.")
                            ->success()
                            ->send();
                    }),

                Action::make('batal_verifikasi')
                    ->label('Hapus Kualifikasi')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Ini akan menghapus akses asisten dari seluruh kelas mata kuliah ini.')
                    ->visible(fn (Matakuliah $mk) =>
                        DB::table('asisten_matakuliah')
                            ->where('id_asisten', $this->record->id_asisten)
                            ->whereIn(
                                'matakuliah_id',
                                Matakuliah::where('nama_mk', $mk->nama_mk)->pluck('id')
                            )
                            ->exists()
                    )
                    ->action(function (Matakuliah $mk) {
                        $ids = Matakuliah::where('nama_mk', $mk->nama_mk)->pluck('id')->toArray();

                        $this->record->matakuliahs()->detach($ids);

                        Notification::make()
                            ->title('Kualifikasi Dihapus')
                            ->success()
                            ->send();
                    }),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $kompetensiIds = DB::table('asisten_kompetensi')
                    ->where('id_asisten', $this->record->id_asisten)
                    ->pluck('id_kompetensi');

                $query->whereExists(function ($q) use ($kompetensiIds) {
                    $q->select(DB::raw(1))
                        ->from('matakuliah_kompetensi')
                        ->whereColumn('matakuliah_kompetensi.matakuliah_id', 'matakuliahs.id')
                        ->whereIn('matakuliah_kompetensi.id_kompetensi', $kompetensiIds);
                });
            });
    }
}
