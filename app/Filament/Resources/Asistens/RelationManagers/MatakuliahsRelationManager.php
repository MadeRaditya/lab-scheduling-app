<?php

namespace App\Filament\Resources\Asistens\RelationManagers;

use App\Models\Matakuliah;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MatakuliahsRelationManager extends RelationManager
{
    protected static string $relationship = 'matakuliahs';

    protected static ?string $title = 'Kualifikasi Mata Kuliah (Verified)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_mk')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_mk')
            ->columns([
                TextColumn::make('kode_mk')
                    ->label('Kode')
                    ->searchable(),
                TextColumn::make('nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable(),
                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('dosen')
                    ->label('Dosen')
                    ->limit(20)
                    ->searchable(),
                TextColumn::make('sks')
                    ->label('SKS'),
                TextColumn::make('semester.nama_semester')
                    ->label('Semester'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Kualifikasi')
                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Pilih Mata Kuliah')
                            ->searchable()
                            ->preload()

                            ->options(function (RelationManager $livewire) {
                                $asistenId = $livewire->getOwnerRecord()->id_asisten;

                                $kompetensiIds = DB::table('asisten_kompetensi')
                                    ->where('id_asisten', $asistenId)
                                    ->pluck('id_kompetensi');

                                return Matakuliah::query()
                                    ->whereHas('kompetensis', function ($q) use ($kompetensiIds) {
                                        $q->whereIn('kompetensis.id_kompetensi', $kompetensiIds);
                                    })
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($mk) {
                                        return [
                                            $mk->id => "{$mk->kode_mk} - {$mk->nama_mk} ({$mk->kelas}) - {$mk->dosen}"
                                        ];
                                    })
                                    ->toArray();
                            })

                            ->getSearchResultsUsing(function (string $search, RelationManager $livewire) {
                                $asistenId = $livewire->getOwnerRecord()->id_asisten;

                                $kompetensiIds = DB::table('asisten_kompetensi')
                                    ->where('id_asisten', $asistenId)
                                    ->pluck('id_kompetensi');

                                return Matakuliah::query()
                                    ->whereHas('kompetensis', function ($q) use ($kompetensiIds) {
                                        $q->whereIn('kompetensis.id_kompetensi', $kompetensiIds);
                                    })
                                    ->where(function ($q) use ($search) {
                                        $q->where('nama_mk', 'like', "%{$search}%")
                                            ->orWhere('kode_mk', 'like', "%{$search}%")
                                            ->orWhere('dosen', 'like', "%{$search}%");
                                    })
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($mk) {
                                        return [
                                            $mk->id => "{$mk->kode_mk} - {$mk->nama_mk} ({$mk->kelas}) - {$mk->dosen}"
                                        ];
                                    })
                                    ->toArray();
                            })

                            ->getOptionLabelUsing(function ($value) {
                                $mk = Matakuliah::find($value);
                                return $mk ? "{$mk->kode_mk} - {$mk->nama_mk} ({$mk->kelas}) - {$mk->dosen}" : null;
                            }),
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
