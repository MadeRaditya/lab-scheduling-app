<?php

namespace App\Filament\Resources\Asistens\RelationManagers;

use App\Models\Kompetensi;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms;

class KompetensiRelationManager extends RelationManager
{
    protected static string $relationship = 'kompetensis';
    protected static ?string $recordTitleAttribute = 'nama_kompetensi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tingkat_penguasaan')
                    ->options(['Dasar' => 'Dasar', 'Menengah' => 'Menengah', 'Ahli' => 'Ahli'])
                    ->required(),
                DatePicker::make('tanggal_sertifikasi')
                    ->nullable(),
                TextInput::make('sertifikasi')
                    ->nullable()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_kompetensi')
            ->columns([
                TextColumn::make('nama_kompetensi')
                    ->label('Kompetensi')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('kategori')
                    ->badge()
                    ->sortable(),
                TextColumn::make('level')
                    ->badge()
                    ->sortable(),
                TextColumn::make('pivot.tingkat_penguasaan')
                    ->label('Tingkat Penguasaan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Ahli' => 'success',
                        'Menengah' => 'warning',
                        'Dasar' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('pivot.tanggal_sertifikasi')
                    ->label('Tanggal Sertifikasi')
                    ->date()
                    ->sortable(),
                TextColumn::make('pivot.sertifikasi')
                    ->label('Sertifikasi')
                    ->limit(30),
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
