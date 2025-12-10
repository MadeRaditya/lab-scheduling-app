<?php

namespace App\Filament\Exports;

use App\Models\JadwalFinal;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class JadwalFinalsExportExporter extends Exporter
{
    protected static ?string $model = JadwalFinal::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make("id")->label("ID"),
            ExportColumn::make("semester.nama_semester")->label("Semester"),
            ExportColumn::make("matakuliah.nama_mk")->label("Mata Kuliah"),
            ExportColumn::make("ruangan.nama_ruangan")->label("Ruangan"),
            ExportColumn::make("hari"),
            ExportColumn::make("jam_mulai"),
            ExportColumn::make("jam_selesai"),
            ExportColumn::make("penugasanAsistenFinals")
                ->label("Asisten Bertugas")
                ->formatStateUsing(
                    fn($state) => $state
                        ->map(fn($asisten) => $asisten->asisten->nama)
                        ->implode(", "),
                ),
            ExportColumn::make("tanggal_dibuat"),
            ExportColumn::make("metode_penjadwalan"),
            ExportColumn::make("status"),
            ExportColumn::make("created_at"),
            ExportColumn::make("updated_at"),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body =
            "Your jadwal finals export export has completed and " .
            Number::format($export->successful_rows) .
            " " .
            str("row")->plural($export->successful_rows) .
            " exported.";

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .=
                " " .
                Number::format($failedRowsCount) .
                " " .
                str("row")->plural($failedRowsCount) .
                " failed to export.";
        }

        return $body;
    }
}
