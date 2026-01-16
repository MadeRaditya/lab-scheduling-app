<?php

namespace App\Filament\Resources\JadwalLabs\Schemas;

use App\Enums\JadwalPrioritas;
use App\Enums\JadwalStatus;
use App\Enums\JamHari;
use App\Models\Asisten;
use App\Models\Matakuliah;
use App\Models\Ruangan;
use App\Models\Semester;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class JadwalLabForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_semester')
                    ->options(
                        Semester::pluck('nama_semester', 'id_semester')
                    )
                    ->searchable()
                    ->required(),
                Select::make('matakuliah_id')
                    ->label('Mata Kuliah')
                    ->required()
                    ->searchable()
                    ->options(function () {
                        return Matakuliah::all()->mapWithKeys(function ($mk) {
                            return [
                                $mk->id => "{$mk->kode_mk} - {$mk->nama_mk} ({$mk->kelas}) - {$mk->dosen}"
                            ];
                        });
                    }),
                Select::make('id_ruangan')
                    ->options(Ruangan::pluck('nama_ruangan', 'id_ruangan'))
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
                TextInput::make('jumlah_mahasiswa')
                    ->required()
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state > 20) {
                            $set('jumlah_asisten_dibutuhkan', 2);
                        } else {
                            $set('jumlah_asisten_dibutuhkan', 1);
                        }
                    }),

                TextInput::make('jumlah_asisten_dibutuhkan')
                    ->required()
                    ->numeric()
                    ->default(1),

                Select::make('prioritas')
                    ->options(JadwalPrioritas::class)
                    ->required()
                    ->default(JadwalPrioritas::Sedang),
                Select::make('status')
                    ->options(JadwalStatus::class)
                    ->required()
                    ->default(JadwalStatus::Draft),
                Textarea::make('keterangan')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
