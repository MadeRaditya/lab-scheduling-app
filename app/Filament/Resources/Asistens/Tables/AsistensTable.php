<?php

namespace App\Filament\Resources\Asistens\Tables;

use App\Filament\Resources\Asistens\AsistenResource;
use App\Models\Asisten;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AsistensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('jurusan')
                    ->searchable(),
                TextColumn::make('kompetensis.nama_kompetensi')
                    ->label('Kompetensi / Status')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->badge()
                    ->state(function (Asisten $record) {
                        $isVerified = $record->matakuliahs()->exists();

                        if (!$isVerified) {
                            return ['Perlu Verifikasi'];
                        }

                        return $record->kompetensis->pluck('nama_kompetensi');
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Perlu Verifikasi' => 'danger',
                        default => 'primary',
                    })
                    ->searchable(),
                IconColumn::make('status_aktif')
                    ->boolean(),
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
                Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->icon('heroicon-m-check-badge')
                    ->color('warning')
                    ->url(fn(Asisten $record) => AsistenResource::getUrl('kualifikasi', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
