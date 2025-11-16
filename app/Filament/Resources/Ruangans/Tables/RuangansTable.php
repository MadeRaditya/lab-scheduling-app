<?php

namespace App\Filament\Resources\Ruangans\Tables;

use App\Enums\RuanganKategori;
use App\Enums\RuanganStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RuangansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_ruangan')
                    ->searchable(),
                TextColumn::make('gedung')
                    ->searchable(),
                TextColumn::make('lantai')
                    ->searchable(),
                TextColumn::make('kapasitas')
                    ->numeric(),
                TextColumn::make('kategori')
                    ->badge()
                    ->color(fn (RuanganKategori $state)=> match ($state) {
                        RuanganKategori::LAB_DASAR => 'success',
                        RuanganKategori::LAB_LANJUT => 'primary',
                        RuanganKategori::LABORATORIUM => 'info',
                        default => 'secondary',
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (RuanganStatus $state)=> match ($state) {
                        RuanganStatus::AKTIF => 'success',
                        RuanganStatus::NON_AKTIF => 'danger',
                        default => 'secondary',
                    })
                    ->sortable()
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
