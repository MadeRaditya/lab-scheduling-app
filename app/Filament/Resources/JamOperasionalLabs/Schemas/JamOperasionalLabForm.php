<?php

namespace App\Filament\Resources\JamOperasionalLabs\Schemas;

use App\Enums\JamHari;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class JamOperasionalLabForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('hari')
                    ->label('Hari')
                    ->options(JamHari::class)
                    ->required(),
                TimePicker::make('jam_buka')
                    ->label('Jam buka')
                    ->required()
                    ->seconds(false),
                TimePicker::make('jam_tutup')
                    ->label('Jam tutup')
                    ->required()
                    ->seconds(false),
                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }
}
