<?php

namespace App\Filament\Resources\PenugasanAsistenFinals\Tables;

use App\Models\Asisten;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PenugasanAsistenFinalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Asisten::query()
                    ->whereHas('penugasanAsistenFinals') 
                    ->withCount('penugasanAsistenFinals') 
            )
            ->defaultSort('penugasan_asisten_finals_count', 'desc') 
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

                TextColumn::make('penugasan_asisten_finals_count')
                    ->label('Total Kelas')
                    ->badge()
                    ->color(fn ($state) => $state > 5 ? 'danger' : ($state > 2 ? 'warning' : 'success'))
                    ->sortable(),

                TextColumn::make('penugasanAsistenFinals.jadwalFinal.matakuliah.nama_mk')
                    ->label('Mata Kuliah Diampu')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->badge()
                    ->color('gray'),
                
                TextColumn::make('avg_skor')
                    ->label('Rata-rata Skor')
                    ->state(function (Asisten $record) {
                        return number_format($record->penugasanAsistenFinals()->avg('skor_kecocokan'), 1);
                    })
                    ->badge()
                    ->color('success'),
            ])
            ->actions([
                Action::make('lihat_jadwal')
                    ->label('Detail Jadwal')
                    ->icon('heroicon-m-eye')
                    ->modalHeading(fn (Asisten $record) => "Jadwal Asistensi: {$record->nama}")
                    ->modalSubmitAction(false)
                    ->modalContent(fn (Asisten $record) => view(
                        'filament.resources.penugasan-asisten-finals.modal-detail-jadwal', 
                        ['asisten' => $record]
                    )),
            ])
            ->bulkActions([]); 
    }
}