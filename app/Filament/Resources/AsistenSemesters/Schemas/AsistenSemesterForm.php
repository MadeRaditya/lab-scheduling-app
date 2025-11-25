<?php

namespace App\Filament\Resources\AsistenSemesters\Schemas;

use App\Enums\AsistenPeran;
use App\Enums\AsistenStatus;
use App\Models\Asisten;
use App\Models\Semester;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;


class AsistenSemesterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_asisten')
                    ->options(
                        Asisten::pluck('nama', 'id_asisten')
                    )
                    ->searchable()
                    ->required(),
                Select::make('id_semester')
                    ->options(
                        Semester::pluck('nama_semester', 'id_semester')
                    )
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->options(AsistenStatus::class)
                    ->required()
                    ->default(AsistenStatus::Aktif),
                Select::make('peran')
                    ->options(AsistenPeran::class)
                    ->required()
                    ->default(AsistenPeran::Asisten),
                DatePicker::make('tanggal_mulai')
                    ->nullable(),
                DatePicker::make('tanggal_keluar')
                    ->nullable(),
                Textarea::make('keterangan')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
