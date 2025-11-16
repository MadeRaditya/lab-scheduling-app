<?php

namespace App\Filament\Resources\Ruangans\Schemas;

use App\Enums\RuanganKategori;
use App\Enums\RuanganStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_ruangan')
                    ->required(),
                TextInput::make('gedung')
                    ->default(null),
                TextInput::make('lantai')
                    ->default(null),
                TextInput::make('kapasitas')
                    ->required()
                    ->numeric()
                    ->default(30),
                Select::make('kategori')
                    ->options(RuanganKategori::class)
                    ->default(null),
                Select::make('status')
                    ->options(RuanganStatus::class)
                    ->default('Aktif')
                    ->required(),
            ]);
    }
}
