<?php

namespace App\Filament\Resources\JadwalFinals\Tables;

use App\Filament\Exports\JadwalFinalsExportExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JadwalFinalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("semester.nama_semester")->sortable(),
                TextColumn::make("matakuliah.nama_mk")->searchable(),
                TextColumn::make("ruangan.nama_ruangan")->sortable(),
                TextColumn::make("hari")->searchable(),
                TextColumn::make("jam_mulai")->time()->sortable(),
                TextColumn::make("jam_selesai")->time()->sortable(),
                TextColumn::make("penugasanAsistenFinals.asisten.nama")
                    ->label("Asisten Bertugas")
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->searchable()
                    ->badge()
                    ->separator(",")
                    ->default("Belum Ada Asisten")
                    ->color(
                        fn($state) => $state === "Belum Ada Asisten"
                            ? "danger"
                            : "primary",
                    ),
                TextColumn::make("tanggal_dibuat")->dateTime()->sortable(),
                TextColumn::make("metode_penjadwalan")->searchable(),
                TextColumn::make("status")->searchable(),
                TextColumn::make("created_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("updated_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([EditAction::make()])
            ->headerActions([
                ExportAction::make()
                    ->exporter(JadwalFinalsExportExporter::class),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(JadwalFinalsExportExporter::class),
                ]),
            ]);
    }
}
