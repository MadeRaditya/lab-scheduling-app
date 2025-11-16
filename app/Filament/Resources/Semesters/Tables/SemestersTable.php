<?php

namespace App\Filament\Resources\Semesters\Tables;

use App\Enums\SemesterJenis;
use App\Enums\SemesterStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SemestersTable
{
    public static function configure(Table $table): Table
    {

    
        return $table
            ->columns([
                TextColumn::make('nama_semester')
                    ->searchable(),
                TextColumn::make('tahun_ajaran')
                    ->searchable(),
                TextColumn::make('jenis')
                    ->badge()
                    ->color(fn (SemesterJenis $state) => match ($state) {
                        SemesterJenis::Ganjil => 'info',
                        SemesterJenis::Genap => 'primary',
                        SemesterJenis::Pendek => 'pink',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (SemesterJenis $state) => $state->value)
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (SemesterStatus $state) => match ($state) {
                        SemesterStatus::Aktif => 'success',
                        SemesterStatus::Rencana => 'warning',
                        SemesterStatus::Selesai => 'gray',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (SemesterStatus $state)=> $state->value)
                    ->searchable(),
                TextColumn::make('tanggal_mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('tanggal_selesai')
                    ->date()
                    ->sortable(),
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
