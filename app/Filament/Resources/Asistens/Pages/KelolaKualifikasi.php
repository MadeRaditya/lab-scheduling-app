<?php

namespace App\Filament\Resources\Asistens\Pages;

use App\Filament\Resources\Asistens\AsistenResource;
use App\Models\Asisten;
use App\Models\Matakuliah;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
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
use Illuminate\Database\Eloquent\Collection;
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
                    Matakuliah::selectRaw('nama_mk, MIN(id) as id')
                        ->groupBy('nama_mk'),
                    'matakuliahs'
                )
            )
            ->modifyQueryUsing(function (Builder $query) {
                $asistenId = $this->record->id_asisten;

                $kompetensiIds = DB::table('asisten_kompetensi')
                    ->where('id_asisten', $asistenId)
                    ->pluck('id_kompetensi');

                $query->whereExists(function ($q) use ($kompetensiIds) {
                    $q->select(DB::raw(1))
                        ->from('matakuliah_kompetensi')
                        ->whereColumn('matakuliah_kompetensi.matakuliah_id', 'matakuliahs.id')
                        ->whereIn('matakuliah_kompetensi.id_kompetensi', $kompetensiIds);
                });

                $query->selectRaw('
                    matakuliahs.*, 
                    (
                        SELECT COUNT(*) 
                        FROM asisten_matakuliah
                        WHERE asisten_matakuliah.id_asisten = ? 
                        AND asisten_matakuliah.matakuliah_id IN (
                            SELECT id FROM matakuliahs as m2 WHERE m2.nama_mk = matakuliahs.nama_mk
                        )
                    ) as is_verified
                ', [$asistenId]);

                $query->orderByDesc('is_verified')->orderBy('nama_mk');
            })
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
                        Notification::make()->title('Berhasil Diverifikasi')->success()->send();
                    }),

                Action::make('batal_verifikasi')
                    ->label('Hapus Kualifikasi')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Matakuliah $mk) =>
                        DB::table('asisten_matakuliah')
                            ->where('id_asisten', $this->record->id_asisten)
                            ->whereIn('matakuliah_id', Matakuliah::where('nama_mk', $mk->nama_mk)->pluck('id'))
                            ->exists()
                    )
                    ->action(function (Matakuliah $mk) {
                        $ids = Matakuliah::where('nama_mk', $mk->nama_mk)->pluck('id')->toArray();
                        $this->record->matakuliahs()->detach($ids);
                        Notification::make()->title('Kualifikasi Dihapus')->success()->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('verify_selected')
                        ->label('Verifikasi Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $names = $records->pluck('nama_mk');
                            
                            $allIds = Matakuliah::whereIn('nama_mk', $names)->pluck('id')->toArray();
                            
                            $this->record->matakuliahs()->syncWithoutDetaching($allIds);
                            
                            Notification::make()
                                ->title('Data Terpilih Berhasil Diverifikasi')
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('unverify_selected')
                        ->label('Hapus Kualifikasi Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $names = $records->pluck('nama_mk');
                            $allIds = Matakuliah::whereIn('nama_mk', $names)->pluck('id')->toArray();
                            
                            $this->record->matakuliahs()->detach($allIds);
                            
                            Notification::make()
                                ->title('Kualifikasi Data Terpilih Dihapus')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
}