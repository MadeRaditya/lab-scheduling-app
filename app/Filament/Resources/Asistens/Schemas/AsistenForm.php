<?php

namespace App\Filament\Resources\Asistens\Schemas;

use App\Enums\JurusanAsisten;
use App\Models\Asisten;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
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
                    ->required()
                    ->unique(Asisten::class, 'npm', fn($record) => $record),
                Select::make('jurusan')
                    ->options(JurusanAsisten::class),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(Asisten::class, 'email', fn($record) => $record),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->required(fn (string $context)=> $context ==='create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->label("Password (leave blank to keep current password)"),
                TextInput::make('no_telepon')
                    ->tel()
                    ->default(null),
                Toggle::make('status_aktif')
                    ->required(),
                DatePicker::make('tanggal_daftar')
                    ->required(),
            ]);
    }
}
