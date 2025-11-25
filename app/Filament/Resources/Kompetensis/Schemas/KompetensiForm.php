<?php

namespace App\Filament\Resources\Kompetensis\Schemas;

use App\Enums\KategoriKompetensi;
use App\Enums\LevelKompetensi;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class KompetensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make("nama_kompetensi")->required(),
            Textarea::make("deskripsi")->default(null)->columnSpanFull(),
            Select::make("kategori")
                ->options(KategoriKompetensi::class)
                ->required(),
            Select::make("level")
                ->options(LevelKompetensi::class)
                ->required()
                ->default("Dasar"),
        ]);
    }
}
