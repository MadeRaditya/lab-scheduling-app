<?php

namespace App\Filament\Widgets;

use App\Models\JadwalFinal;
use App\Enums\SemesterStatus;
use App\Filament\Resources\JadwalFinals\JadwalFinalResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Actions\Action;

class JadwalBelumAdaAsisten extends BaseWidget
{
    protected static ?int $sort = 2; 
    
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '⚠️ PERHATIAN: Jadwal Belum Dapat Asisten';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JadwalFinal::query()
                    ->with(['matakuliah', 'semester'])
                    ->whereHas('semester', function ($q) {
                        $q->where('status', SemesterStatus::Aktif);
                    })
                    ->doesntHave('penugasanAsistenFinals')
                    ->orderBy('hari', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('hari')
                    ->badge()
                    ->color('danger'),
                
                Tables\Columns\TextColumn::make('jam_mulai')
                    ->label('Waktu')
                    ->formatStateUsing(fn ($record) => "{$record->jam_mulai} - {$record->jam_selesai}"),

                Tables\Columns\TextColumn::make('matakuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->description(fn ($record) => "Kelas: {$record->matakuliah->kelas}"),

                Tables\Columns\TextColumn::make('ruangan.nama_ruangan')
                    ->label('Ruangan'),
            ])
            ->actions([
                Action::make('assign')
                    ->label('Tugaskan')
                    ->icon('heroicon-m-user-plus')
                    ->button()
                    ->color('danger')
                    ->url(fn (JadwalFinal $record): string => JadwalFinalResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false)
            ->defaultPaginationPageOption(5); 
    }
}