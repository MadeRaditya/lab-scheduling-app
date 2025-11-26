<?php

namespace App\Filament\Resources\Asistens\RelationManagers;

use App\Enums\JamHari;
use App\Enums\PreferensiTingkat;
use App\Models\Semester;
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
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PreferensiJadwalAsistenRelationManager extends RelationManager
{
    protected static string $relationship = 'preferensiJadwalAsistens';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make("id_semester")
                    ->options(
                        Semester::pluck('nama_semester', 'id_semester')
                    )
                    ->searchable()
                    ->required(),
                Select::make('hari')
                    ->options(JamHari::class)
                    ->required(),
                TimePicker::make('jam_mulai')
                    ->seconds(false)
                    ->required(),
                TimePicker::make('jam_selesai')
                    ->seconds(false)
                    ->required(),
                Select::make('tingkat_preferensi')
                    ->options(PreferensiTingkat::class)
                    ->required()
                    ->default(PreferensiTingkat::Bisa),
                TextInput::make('keterangan')
                    ->default(null),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id_asisten')
            ->columns([
                TextColumn::make('semester.nama_semester')
                    ->sortable(),
                TextColumn::make('hari')
                    ->searchable(),
                TextColumn::make('jam_mulai')
                    ->time(),
                TextColumn::make('jam_selesai')
                    ->time(),
                TextColumn::make('tingkat_preferensi'),
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
