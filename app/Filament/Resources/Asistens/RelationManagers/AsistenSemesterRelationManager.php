<?php

namespace App\Filament\Resources\Asistens\RelationManagers;

use App\Enums\AsistenPeran;
use App\Enums\AsistenStatus;
use App\Models\Semester;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AsistenSemesterRelationManager extends RelationManager
{
    protected static string $relationship = 'asistenSemesters';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_semester')
                    ->options(
                        Semester::pluck('nama_semester', 'id_semester')
                    )
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->options(AsistenStatus::class)
                    ->required()
                    ->default(AsistenStatus::Aktif),
                Select::make('peran')
                    ->options(AsistenPeran::class)
                    ->required()
                    ->default(AsistenPeran::Asisten),
                DatePicker::make('tanggal_mulai')
                    ->nullable(),
                DatePicker::make('tanggal_keluar')
                    ->nullable(),
                Textarea::make('keterangan')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id_asisten')
            ->columns([
                TextColumn::make('semester.nama_semester'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('peran')
                    ->searchable(),
                TextColumn::make('tanggal_mulai')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tanggal_keluar')
                    ->date()
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
