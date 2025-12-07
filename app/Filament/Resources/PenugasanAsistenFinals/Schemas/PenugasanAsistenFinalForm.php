<?php

namespace App\Filament\Resources\PenugasanAsistenFinals\Schemas;

use App\Enums\AsistenPeran;
use App\Enums\PenugasanPeran;
use App\Models\Asisten;
use App\Models\JadwalFinal;
use App\Models\Kompetensi;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PenugasanAsistenFinalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_jadwal_final')
                    ->options(
                        JadwalFinal::with('matakuliah')
                            ->get()
                            ->mapWithKeys(fn($j) => [
                                $j->id_final => "{$j->matakuliah->nama_mk} – {$j->hari} ({$j->jam_mulai}–{$j->jam_selesai})",
                            ])
                    )
                    ->searchable()
                    ->required(),
                Select::make('id_asisten')
                    ->options(Asisten::pluck('nama', 'id_asisten'))
                    ->searchable()
                    ->required(),
                Select::make('id_kompetensi')
                    ->options(Kompetensi::pluck('nama_kompetensi', 'id_kompetensi'))
                    ->default(null),
                TextInput::make('skor_kecocokan')
                    ->required()
                    ->numeric()
                    ->default(1.0),
                Select::make('peran')
                    ->options(PenugasanPeran::class)
                    ->required()
                    ->default(PenugasanPeran::Utama),
                Select::make('status_kehadiran')
                    ->options(['Terjadwal' => 'Terjadwal', 'Hadir' => 'Hadir', 'Tidak Hadir' => 'Tidak Hadir', 'Izin' => 'Izin'])
                    ->required()
                    ->default('Terjadwal'),
                Textarea::make('catatan')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
