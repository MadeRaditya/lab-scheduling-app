<?php

namespace App\Filament\Resources\Matakuliahs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MatakuliahsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_mk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_mk')
                    ->label("Mata Kuliah")
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                TextColumn::make('asistens_count')
                    ->counts('asistens')
                    ->label('Jml Asisten')
                    ->badge()
                    ->color(fn(string $state): string => match (true) {
                        $state == 0 => 'danger',
                        $state < 2  => 'warning',
                        default     => 'success',
                    })
                    ->sortable(),
                TextColumn::make('kelas')
                    ->badge()
                    ->color("info")
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sks')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('dosen')
                    ->searchable(),
                TextColumn::make('semester.nama_semester')
                    ->sortable(),
                TextColumn::make('kompetensis.nama_kompetensi')
                    ->label("Kompetensi")
                    ->badge()
                    ->color("success")
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
