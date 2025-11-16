<?php

namespace App\Filament\Resources\Asistens\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AsistenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                TextInput::make('npm')
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('no_telepon')
                    ->tel()
                    ->default(null),
                Toggle::make('status_aktif')
                    ->required(),
                DateTimePicker::make('tanggal_daftar')
                    ->required(),
            ]);
    }
}
