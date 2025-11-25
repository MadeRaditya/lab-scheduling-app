<?php

namespace App\Filament\Resources\Semesters\Schemas;

use App\Enums\SemesterJenis;
use App\Enums\SemesterStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SemesterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_semester')
                    ->maxLength(100)
                    ->required(),
                TextInput::make('tahun_ajaran')
                    ->maxLength(20)
                    ->required(),
                Select::make('jenis')
                    ->options(SemesterJenis::class)
                    ->required(),
                Select::make('status')
                    ->options(SemesterStatus::class)
                    ->required()
                    ->default(SemesterStatus::Rencana),
                DatePicker::make('tanggal_mulai'),
                DatePicker::make('tanggal_selesai'),
            ]);
    }
}
