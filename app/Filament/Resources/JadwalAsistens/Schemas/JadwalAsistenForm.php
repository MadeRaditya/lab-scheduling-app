<?php

namespace App\Filament\Resources\JadwalAsistens\Schemas;

use App\Enums\JamHari;
use App\Models\Asisten;
use App\Models\Semester;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class JadwalAsistenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make("id_asisten")
                    ->options(Asisten::pluck('nama', 'id_asisten'))
                    ->searchable()
                    ->required(),
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
                TextInput::make("keterangan")
                    ->maxLength(100)
                    ->nullable(),
                Select::make('jenis_kegiatan')
                    ->options(['Kuliah' => 'Kuliah', 'Organisasi' => 'Organisasi', 'Pribadi' => 'Pribadi', 'Lainnya' => 'Lainnya'])
                    ->default('Kuliah')
            ]);
    }
}
