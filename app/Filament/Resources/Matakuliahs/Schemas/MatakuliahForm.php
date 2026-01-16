<?php

namespace App\Filament\Resources\Matakuliahs\Schemas;

use App\Enums\SemesterStatus;
use App\Models\Semester;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MatakuliahForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_mk')
                ->label("Kode MK")
                ->maxLength(10)
                ->required(),
                TextInput::make('nama_mk')
                    ->required(),
                TextInput::make('sks')
                    ->required()
                    ->minValue(0)
                    ->numeric(),
                TextInput::make('kelas')
                    ->default(null),
                TextInput::make('dosen')
                    ->required(),
                Select::make('semester_berjalan')
                ->options(
                    Semester::where('status', SemesterStatus::Aktif)
                    ->pluck('nama_semester', 'id_semester')
                )
                    ->required(),
                Textarea::make('deskripsi')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
