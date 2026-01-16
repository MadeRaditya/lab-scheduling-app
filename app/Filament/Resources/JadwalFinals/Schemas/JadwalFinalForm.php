<?php

namespace App\Filament\Resources\JadwalFinals\Schemas;

use App\Enums\JamHari;
use App\Models\Matakuliah;
use App\Models\Ruangan;
use App\Models\Semester;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class JadwalFinalForm
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
                DateTimePicker::make('tanggal_dibuat')
                    ->default(now())
                    ->required(),
                TextInput::make('metode_penjadwalan')
                    ->required()
                    ->default('Greedy Algorithm'),
                Select::make('status')
                    ->options(['Aktif' => 'Aktif', 'Selesai' => 'Selesai', 'Dibatalkan' => 'Dibatalkan'])
                    ->default('Aktif'),
                Textarea::make('catatan')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
