<?php

namespace App\Filament\Resources\PenugasanAsistenFinals\Tables;

use App\Models\PenugasanAsistenFinal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PenugasanAsistenFinalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc') 
            ->columns([
                TextColumn::make('jadwalFinal.matakuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->description(fn (PenugasanAsistenFinal $record) => 
                        "{$record->jadwalFinal->matakuliah->kode_mk} ({$record->jadwalFinal->matakuliah->kelas}) - {$record->jadwalFinal->matakuliah->dosen}"
                    )
                    ->wrap()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('asisten.nama')
                    ->label('Nama Asisten')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('asisten.kompetensis.nama_kompetensi')
                    ->label("Kompetensi Asisten")
                    ->badge()
                    ->color("success")
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->searchable(), 

                TextColumn::make('skor_kecocokan')
                    ->label('Skor')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state > 80 ? 'success' : ($state > 50 ? 'warning' : 'danger')),

                TextColumn::make('peran')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('status_kehadiran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Izin' => 'warning',
                        'Alpha' => 'danger',
                        'Terjadwal' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}