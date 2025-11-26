<?php

namespace App\Filament\Resources\Matakuliahs\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KompetensiRelationManager extends RelationManager
{
    protected static string $relationship = 'kompetensis';
    protected static ?string $recordTitleAttribute = 'nama_kompetensi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tingkat_dibutuhkan')
                    ->options(['Dasar' => 'Dasar', 'Menengah' => 'Menengah', 'Lanjut' => 'Lanjut'])
                    ->required(),
                Select::make('prioritas')
                    ->options([
                        1 => 'Wajib',
                        2 => 'Disarankan',
                        3 => 'Opsional'
                    ])->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_kompetensi')
            ->columns([
                TextColumn::make('nama_kompetensi')->label('Kompetensi')->sortable()->searchable(),
                TextColumn::make('pivot.tingkat_dibutuhkan'),
                TextColumn::make('pivot.prioritas')
                    ->formatStateUsing(fn($state) => match ($state) {
                        1 => 'Wajib',
                        2 => 'Disarankan',
                        3 => 'Opsional',
                        default => $state
                    })->badge()->color(fn($state) => match ($state) {
                        1 => 'danger',
                        2 => 'warning',
                        3 => 'gray',
                        default => 'gray'
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // CreateAction::make(),
                AttachAction::make()->preloadRecordSelect(),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
