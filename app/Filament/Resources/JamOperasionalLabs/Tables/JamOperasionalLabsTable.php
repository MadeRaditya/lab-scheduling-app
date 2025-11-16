<?php

namespace App\Filament\Resources\JamOperasionalLabs\Tables;

use App\Enums\JamHari;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JamOperasionalLabsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hari')
                    ->label('Hari')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jam_buka')
                    ->label('Jam buka')
                    ->time()
                    ->sortable(),
                TextColumn::make('jam_tutup')
                    ->label('Jam tutup')
                    ->time()
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
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
