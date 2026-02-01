<?php

namespace App\Filament\Resources\PreferensiJadwalAsistens\Tables;

use App\Models\Asisten;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PreferensiJadwalAsistensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Asisten::query()
                    ->whereHas('preferensiJadwalAsistens') 
                    ->withCount('preferensiJadwalAsistens') 
            )
            ->defaultSort('preferensi_jadwal_asistens_count', 'desc')
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

                TextColumn::make('preferensi_jadwal_asistens_count')
                    ->label('Slot Tersedia')
                    ->badge()
                    ->color(fn ($state) => $state > 20 ? 'success' : ($state > 10 ? 'warning' : 'danger'))
                    ->sortable(),

                TextColumn::make('preferensiJadwalAsistens.hari')
                    ->label('Hari Ketersediaan')
                    ->badge()
                    ->separator(',')
                    ->limitList(4)
                    ->color('gray'),
            ])
            ->filters([
            ])
            ->actions([
                Action::make('lihat_preferensi')
                    ->label('Detail Preferensi')
                    ->icon('heroicon-m-clock')
                    ->modalHeading(fn (Asisten $record) => "Preferensi Waktu: {$record->nama}")
                    ->modalSubmitAction(false)
                    ->modalContent(fn (Asisten $record) => view(
                        'filament.resources.preferensi-jadwal-asistens.modal-detail-preferensi', 
                        ['asisten' => $record]
                    )),
            ])
            ->bulkActions([]); 
    }
}