<?php

namespace App\Filament\Resources\CalonAsistens\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CalonAsistenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')->required(),
                TextInput::make('npm')->required()->unique(ignoreRecord: true),
                TextInput::make('email')->email(),
            ]);
    }
}
