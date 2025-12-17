<?php

namespace App\Filament\Resources\PenugasanAsistenFinals\Tables;

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
            ->columns([
                TextColumn::make('jadwalFinal.matakuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('asisten.nama')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('kompetensi.nama_kompetensi')
                    ->sortable(),

                TextColumn::make('skor_kecocokan')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('peran')
                    ->searchable(),

                TextColumn::make('status_kehadiran')
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
