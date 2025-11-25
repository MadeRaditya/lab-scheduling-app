<?php

namespace App\Filament\Resources\KendalaAsistens\Schemas;

use App\Enums\AsistenStatus;
use App\Enums\JamHari;
use App\Enums\KendalaTipe;
use App\Models\Asisten;
use App\Models\Semester;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class KendalaAsistenForm
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
                    ->options(AsistenStatus::class)
                    ->required()
                    ->default(AsistenStatus::Aktif),
            ]);
    }
}
