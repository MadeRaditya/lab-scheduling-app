<?php

namespace App\Filament\Resources\Asistens\RelationManagers;

use App\Enums\AsistenStatus;
use App\Enums\JamHari;
use App\Enums\KendalaTipe;
use App\Enums\StatusKendala;
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

class KendalaAsistensRelationManager extends RelationManager
{
    protected static string $relationship = 'kendalaAsistens';

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
                Select::make('tipe_kendala')
                    ->options(KendalaTipe::class)
                    ->required()
                    ->default(KendalaTipe::Lainnya),
                Select::make('hari')
                    ->options(JamHari::class)
                    ->required(),
                DatePicker::make('tanggal_mulai')
                    ->seconds(false)
                    ->required(),
                DatePicker::make('tanggal_selesai')
                    ->seconds(false)
                    ->required(),
                Textarea::make('alasan')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(StatusKendala::class)
                    ->required()
                    ->default(StatusKendala::Pending),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id_asisten')
            ->columns([
                TextColumn::make('tipe_kendala'),
                TextColumn::make('hari'),
                TextColumn::make('tanggal_mulai')->date(),
                TextColumn::make('tanggal_selesai')->date(),
                TextColumn::make('status')->badge()->color(fn(string $state): string => match ($state) {
                    'Aktif' => 'warning',
                    'Selesai' => 'success',
                    default => 'gray',
                }),
                TextColumn::make('alasan')
                ->limit(50)
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
