<?php

namespace App\Filament\Resources\JadwalAsistens\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JadwalAsistensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asisten.nama')->label('Asisten')
                ->searchable(),
                TextColumn::make('semester.nama_semester')->label('Semester'),
                TextColumn::make('hari')
                ->sortable()
                ->searchable(),
                TextColumn::make('jam_mulai')->time()
                ->sortable(),
                TextColumn::make('jam_selesai')->time(),
                TextColumn::make('jenis_kegiatan')->label('Jenis Kegiatan')
            ])
            ->filters([])
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
