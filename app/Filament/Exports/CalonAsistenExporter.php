<?php

namespace App\Filament\Exports;

use App\Models\CalonAsisten;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class CalonAsistenExporter extends Exporter
{
    protected static ?string $model = CalonAsisten::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
                
            ExportColumn::make('nama')
                ->label('Nama Lengkap'),
                
            ExportColumn::make('npm')
                ->label('NPM'),
                
            ExportColumn::make('token')
                ->label('Token Registrasi'),
                
            ExportColumn::make('is_registered')
                ->label('Status Pendaftaran')
                ->formatStateUsing(fn ($state) => $state ? 'Sudah Mendaftar' : 'Belum Mendaftar'),
                
            ExportColumn::make('created_at')
                ->label('Dibuat Pada')
                ->enabledByDefault(false), 
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export data calon asisten selesai. ' . Number::format($export->successful_rows) . ' baris berhasil diexport.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diexport.';
        }

        return $body;
    }
}