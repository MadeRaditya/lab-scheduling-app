<?php

namespace App\Filament\Resources\JadwalFinals\Tables;

use App\Filament\Exports\JadwalFinalsExportExporter;
use App\Models\JadwalFinal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JadwalFinalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('hari', 'desc')
            ->columns([
                TextColumn::make("semester.nama_semester")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make("matakuliah.nama_mk")
                    ->label('Mata Kuliah')
                    ->description(fn(JadwalFinal $record) => "{$record->matakuliah->kode_mk} - {$record->matakuliah->dosen}")
                    ->searchable()
                    ->wrap(),

                TextColumn::make("matakuliah.kelas")
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),

                TextColumn::make("ruangan.nama_ruangan")
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make("hari")
                    ->searchable(),

                TextColumn::make("jam_mulai")
                    ->label('Waktu')
                    ->formatStateUsing(fn(JadwalFinal $record) => "{$record->jam_mulai} - {$record->jam_selesai}")
                    ->sortable(),

                TextColumn::make("penugasanAsistenFinals.asisten.nama")
                    ->label("Asisten")
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->searchable()
                    ->badge()
                    ->default("Belum Ada")
                    ->color(fn($state) => $state === "Belum Ada" ? "danger" : "primary"),

                TextColumn::make("penugasanAsistenFinals.skor_kecocokan")
                    ->label("Skor")
                    ->listWithLineBreaks()
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->alignCenter(),

                TextColumn::make("status")
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Selesai' => 'gray',
                        'Dibatalkan' => 'danger',
                        default => 'info',
                    })
                    ->searchable(),
            ])
            ->filters([
                TernaryFilter::make('status_asisten')
                ->label('Status Asisten')
                    ->placeholder('Semua Jadwal')
                    ->trueLabel('Sudah Ada Asisten')
                    ->falseLabel('Belum Ada Asisten')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('penugasanAsistenFinals'),
                        false: fn (Builder $query) => $query->whereDoesntHave('penugasanAsistenFinals'),
                    ),
            ])
            ->recordActions([EditAction::make()])
            ->headerActions([
                ExportAction::make()
                    ->exporter(JadwalFinalsExportExporter::class)
                    ->label('Cetak Jadwal')
                    ->icon('heroicon-o-printer')
                    ->color('success')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(JadwalFinalsExportExporter::class)
                        ->label('Cetak Terpilih')
                        ->icon('heroicon-o-printer'),
                ]),
            ]);
    }
}
