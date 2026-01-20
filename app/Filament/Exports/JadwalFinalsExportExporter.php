<?php

namespace App\Filament\Exports;

use App\Models\JadwalFinal;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class JadwalFinalsExportExporter extends Exporter
{
    protected static ?string $model = JadwalFinal::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('semester.nama_semester')
                ->label('Semester'),

            ExportColumn::make('matakuliah.nama_mk')
                ->label('Mata Kuliah'),

            ExportColumn::make('matakuliah.kelas')
                ->label('Kelas'),

            ExportColumn::make('matakuliah.dosen')
                ->label('Dosen Pengampu'),

            ExportColumn::make('ruangan.nama_ruangan')
                ->label('Ruangan'),

            ExportColumn::make('hari')
                ->label('Hari'),

            ExportColumn::make('waktu')
                ->label('Waktu')
                ->state(fn (JadwalFinal $record): string => "{$record->jam_mulai} - {$record->jam_selesai}"),

            ExportColumn::make('asisten_bertugas')
                ->label('Asisten')
                ->state(function (JadwalFinal $record): string {
                    $asisten = $record->penugasanAsistenFinals->map(function ($penugasan) {
                        return $penugasan->asisten->nama;
                    })->toArray();

                    return empty($asisten) ? 'Belum Ada Asisten' : implode(', ', $asisten);
                }),

            ExportColumn::make('status')
                ->label('Status Jadwal')
                ->enabledByDefault(false), 

            ExportColumn::make('rata_rata_skor')
                ->label('Skor Kecocokan')
                ->state(fn (JadwalFinal $record) => $record->penugasanAsistenFinals->avg('skor_kecocokan'))
                ->enabledByDefault(false),
            
            ExportColumn::make('tanggal_dibuat')
                ->label('Tgl Generate')
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Jadwal Final berhasil di-export dan ' . number_format($export->successful_rows) . ' baris telah diproses.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal di-export.';
        }

        return $body;
    }
}