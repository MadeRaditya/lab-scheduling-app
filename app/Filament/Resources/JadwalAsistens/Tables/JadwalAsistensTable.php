<?php

namespace App\Filament\Resources\JadwalAsistens\Tables;

use App\Models\Asisten;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JadwalAsistensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Asisten::query()
                    ->whereHas('jadwalAsistens')
                    ->withCount('jadwalAsistens')
            )
            ->defaultSort('jadwal_asistens_count', 'desc')
            ->recordUrl(null) 
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Asisten')
                    ->description(fn (Asisten $record) => $record->npm)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jurusan')
                    ->label('Jurusan')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('jadwal_asistens_count')
                    ->label('Total Kegiatan')
                    ->badge()
                    ->color(fn ($state) => $state > 15 ? 'danger' : ($state > 10 ? 'warning' : 'success'))
                    ->sortable(),

                TextColumn::make('jadwalAsistens.hari')
                    ->label('Hari Sibuk')
                    ->badge()
                    ->separator(',')
                    ->limitList(4)
                    ->color('gray'),
            ])
            ->filters([])
            ->actions([
                Action::make('lihat_krs')
                    ->label('Detail KRS')
                    ->icon('heroicon-m-calendar-days')
                    ->modalHeading(fn (Asisten $record) => "Jadwal Kesibukan: {$record->nama}")
                    ->modalSubmitAction(false)
                    ->modalContent(fn (Asisten $record) => view(
                        'filament.resources.jadwal-asistens.modal-detail-krs', 
                        ['asisten' => $record]
                    )),
            ])
            ->bulkActions([]);
    }
}