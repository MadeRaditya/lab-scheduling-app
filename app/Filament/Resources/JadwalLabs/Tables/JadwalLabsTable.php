<?php

namespace App\Filament\Resources\JadwalLabs\Tables;

use App\Models\JadwalLab;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JadwalLabsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('semester.nama_semester')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('matakuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->wrap()
                    ->description(fn(JadwalLab $record) => $record->ruangan->nama_ruangan ?? '-'),

                TextColumn::make('hari')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('waktu')
                    ->label('Waktu')
                    ->state(fn(JadwalLab $record) => "{$record->jam_mulai} - {$record->jam_selesai}")
                    ->sortable(['jam_mulai']),

                TextColumn::make('kapasitas_info')
                    ->label('Mhs / Asisten')
                    ->state(fn(JadwalLab $record) => "{$record->jumlah_mahasiswa} Mhs / {$record->jumlah_asisten_dibutuhkan} Ast")
                    ->badge()
                    ->color('gray'),

                TextColumn::make('prioritas')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Tinggi' => 'danger',
                        'Sedang' => 'warning',
                        default => 'success',
                    })
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Draft' => 'gray',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default => 'primary',
                    })
                    ->searchable(),

                TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
