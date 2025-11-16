<?php

namespace App\Filament\Resources\Kompetensis\Tables;

use App\Enums\LevelKompetensi;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KompetensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kompetensi')
                    ->searchable(),
                TextColumn::make('kategori')
                    ->searchable(),
                TextColumn::make('level')
                    ->badge()
                    ->color(fn (LevelKompetensi $state) => match ($state) {
                        LevelKompetensi::Dasar => 'info',
                        LevelKompetensi::Menengah => 'primary',
                        LevelKompetensi::Lanjut => 'success',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (LevelKompetensi $state) => $state->value)
                    ->searchable(),
                TextColumn::make('deskripsi')
                    ->wrap()
                    ->lineClamp(2)
                    ->toggleable(isToggledHiddenByDefault: true),
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
