<?php

namespace App\Filament\Resources\JadwalFinals\RelationManagers;

use App\Enums\PenugasanPeran;
use App\Models\Asisten;
use App\Models\Kompetensi;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PenugasanAsistenFinalsRelationManager extends RelationManager
{
    protected static string $relationship = 'penugasanAsistenFinals';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_asisten')
                    ->options(Asisten::pluck('nama', 'id_asisten'))
                    ->searchable()
                    ->required(),
                Select::make('id_kompetensi')
                    ->options(Kompetensi::pluck('nama_kompetensi', 'id_kompetensi'))
                    ->default(null),
                TextInput::make('skor_kecocokan')
                    ->required()
                    ->numeric()
                    ->default(1.0),
                Select::make('peran')
                    ->options(PenugasanPeran::class)
                    ->required()
                    ->default(PenugasanPeran::Utama),
                Select::make('status_kehadiran')
                    ->options(['Terjadwal' => 'Terjadwal', 'Hadir' => 'Hadir', 'Tidak Hadir' => 'Tidak Hadir', 'Izin' => 'Izin'])
                    ->required()
                    ->default('Terjadwal'),
                Textarea::make('catatan')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id_jadwal_final')
            ->columns([
                TextColumn::make('asisten.nama')
                    ->sortable(),
                TextColumn::make('kompetensi.nama_kompetensi')
                    ->sortable(),
                TextColumn::make('skor_kecocokan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('peran')
                    ->searchable(),
                TextColumn::make('status_kehadiran')
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
            ->headerActions([
                CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                // DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
