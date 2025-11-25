<?php

namespace App\Filament\Resources\Asistens\RelationManagers;

use App\Enums\JamHari;
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

class JadwalAsistenRelationManager extends RelationManager
{
    protected static string $relationship = 'jadwalAsistens';

    protected static ?string $title = 'Jadwal Asisten';


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_semester')
                    ->options(Semester::where('status', 'Aktif')->pluck('nama_semester', 'id_semester'))
                    ->required()
                    ->label('Semester'),
                Select::make('hari')
                    ->options(JamHari::class)
                    ->required(),
                TimePicker::make('jam_mulai')->required()
                    ->seconds(false),
                TimePicker::make('jam_selesai')->required()
                    ->seconds(false),
                TextInput::make('jenis_kegiatan')->default('Kuliah')
                    ->required(),
                TextInput::make('keterangan')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('hari')
            ->columns([
                TextColumn::make('semester.nama_semester')
                    ->label('Semester')
                    ->sortable(),
                TextColumn::make('hari')
                    ->badge()
                    ->sortable(),
                TextColumn::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->time('H:i'),
                TextColumn::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->time('H:i'),
                TextColumn::make('jenis_kegiatan')
                    ->label('Jenis Kegiatan')
                    ->badge(),
                TextColumn::make('keterangan')
                    ->limit(30)
                    ->toggleable(),
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
            ])
            ->defaultSort('hari', 'asc');
    }
}
