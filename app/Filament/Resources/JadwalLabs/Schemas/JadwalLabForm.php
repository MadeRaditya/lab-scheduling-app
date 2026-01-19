<?php

namespace App\Filament\Resources\JadwalLabs\Schemas;

use App\Enums\JadwalPrioritas;
use App\Enums\JadwalStatus;
use App\Enums\JamHari;
use App\Models\JadwalLab;
use App\Models\Matakuliah;
use App\Models\Ruangan;
use App\Models\Semester;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Get;
use Filament\Schemas\Components\Utilities\Get as UtilitiesGet;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class JadwalLabForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_semester')
                    ->label('Semester')
                    ->options(Semester::pluck('nama_semester', 'id_semester'))
                    ->searchable()
                    ->required()
                    ->default(fn() => Semester::where('status', 'Aktif')->first()?->id_semester),

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

                Select::make('hari')
                    ->options(JamHari::class)
                    ->required()
                    ->live(),

                TimePicker::make('jam_mulai')
                    ->seconds(false)
                    ->required()
                    ->live(),

                TimePicker::make('jam_selesai')
                    ->seconds(false)
                    ->required()
                    ->live(),

                Select::make('id_ruangan')
                    ->label('Ruangan (Tersedia)')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(function (UtilitiesGet $get, ?JadwalLab $record) {
                        $hari = $get('hari');
                        $mulai = $get('jam_mulai');
                        $selesai = $get('jam_selesai');

                        if (!$hari || !$mulai || !$selesai) {
                            return Ruangan::pluck('nama_ruangan', 'id_ruangan');
                        }

                        return Ruangan::whereDoesntHave('jadwalLabs', function (Builder $query) use ($hari, $mulai, $selesai, $record) {
                            $query->where('hari', $hari)
                                ->where(function ($qTime) use ($mulai, $selesai) {
                                    $qTime->where('jam_mulai', '<', $selesai)
                                        ->where('jam_selesai', '>', $mulai);
                                });

                            if ($record) {
                                $query->where('id_jadwal', '!=', $record->id_jadwal);
                            }
                        })
                            ->pluck('nama_ruangan', 'id_ruangan');
                    }),

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
