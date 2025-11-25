<?php

namespace App\Filament\Resources\PreferensiJadwalAsistens\Schemas;

use App\Enums\JamHari;
use App\Enums\PreferensiTingkat;
use App\Models\Asisten;
use App\Models\Semester;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class PreferensiJadwalAsistenForm
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
                Select::make('tingkat_preferensi')
                    ->options(PreferensiTingkat::class)
                    ->required()
                    ->default(PreferensiTingkat::Bisa),
                TextInput::make('keterangan')
                    ->default(null),
            ]);
    }
}
