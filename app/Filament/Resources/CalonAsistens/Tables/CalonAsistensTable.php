<?php

namespace App\Filament\Resources\CalonAsistens\Tables;

use App\Filament\Exports\CalonAsistenExporter;
use App\Models\CalonAsisten;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CalonAsistensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('npm')->searchable()->sortable(),
                TextColumn::make('token')
                    ->copyable()
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray'),
                IconColumn::make('is_registered')
                    ->boolean()
                    ->label('Sudah Daftar?'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('generate_token')
                    ->label('Generate Token')
                    ->icon('heroicon-o-key')
                    ->color('info')
                    ->action(function (CalonAsisten $record) {
                        $token = strtoupper(substr(Str::uuid(), 0, 32));

                        $record->update(['token' => $token]);

                        Notification::make()
                            ->title('Token Berhasil Dibuat')
                            ->body("Token: $token")
                            ->success()
                            ->send();
                    })
                    ->visible(fn(CalonAsisten $record) => !$record->is_registered),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(CalonAsistenExporter::class)
                    ->label('Export Data')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
