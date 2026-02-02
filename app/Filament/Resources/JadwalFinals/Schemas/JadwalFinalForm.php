<?php

namespace App\Filament\Resources\JadwalFinals\Schemas;

use App\Enums\JamHari;
use App\Models\JadwalFinal;
use App\Models\Matakuliah;
use App\Models\Ruangan;
use App\Models\Semester;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Closure;
use Filament\Schemas\Components\Utilities\Get;

class JadwalFinalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_semester')
                    ->label('Semester')
                    ->options(Semester::pluck('nama_semester', 'id_semester'))
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

                
                
                Select::make('hari')
                    ->options(JamHari::class)
                    ->required()
                    ->live() 
                    ->afterStateUpdated(fn ($state, callable $set) => $set('id_ruangan', null)),

                TimePicker::make('jam_mulai')
                    ->seconds(false)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('id_ruangan', null)),

                TimePicker::make('jam_selesai')
                    ->seconds(false)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('id_ruangan', null))
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            if ($get('jam_mulai') && $value <= $get('jam_mulai')) {
                                $fail('Jam selesai harus lebih akhir dari jam mulai.');
                            }
                        },
                    ]),

                Select::make('id_ruangan')
                    ->label('Ruangan (Otomatis Filter)')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->rules([
                        fn (Get $get, ?JadwalFinal $record): Closure => function (string $attribute, $value, Closure $fail) use ($get, $record) {
                            $hari = $get('hari');
                            $mulai = $get('jam_mulai');
                            $selesai = $get('jam_selesai');

                            if (!$hari || !$mulai || !$selesai) return;

                            $conflict = JadwalFinal::where('id_ruangan', $value)
                                ->where('hari', $hari)
                                ->where(function ($query) use ($mulai, $selesai) {
                                    $query->where('jam_mulai', '<', $selesai)
                                          ->where('jam_selesai', '>', $mulai);
                                })
                                ->when($record, fn($q) => $q->where('id_final', '!=', $record->id_final)) 
                                ->where('status', '!=', 'Dibatalkan')
                                ->first();

                            if ($conflict) {
                                $fail("Ruangan bentrok dengan mata kuliah: {$conflict->matakuliah->nama_mk} ({$conflict->jam_mulai} - {$conflict->jam_selesai}).");
                            }
                        },
                    ])
                    ->options(function (Get $get, ?JadwalFinal $record) {
                        $hari = $get('hari');
                        $mulai = $get('jam_mulai');
                        $selesai = $get('jam_selesai');

                        if (!$hari || !$mulai || !$selesai) {
                            return Ruangan::pluck('nama_ruangan', 'id_ruangan');
                        }

                        return Ruangan::whereDoesntHave('jadwalFinals', function (Builder $query) use ($hari, $mulai, $selesai, $record) {
                            $query->where('hari', $hari)
                                  ->where(function ($qTime) use ($mulai, $selesai) {
                                      $qTime->where('jam_mulai', '<', $selesai)
                                            ->where('jam_selesai', '>', $mulai);
                                  })
                                  ->where('status', '!=', 'Dibatalkan');

                            if ($record) {
                                $query->where('id_final', '!=', $record->id_final);
                            }
                        })
                        ->pluck('nama_ruangan', 'id_ruangan');
                    }),

                DateTimePicker::make('tanggal_dibuat')
                    ->default(now())
                    ->required(),

                TextInput::make('metode_penjadwalan')
                    ->required()
                    ->default('Manual Edit (Revisi SKS)'),

                Select::make('status')
                    ->options(['Aktif' => 'Aktif', 'Selesai' => 'Selesai', 'Dibatalkan' => 'Dibatalkan'])
                    ->default('Aktif'),

                Textarea::make('catatan')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}