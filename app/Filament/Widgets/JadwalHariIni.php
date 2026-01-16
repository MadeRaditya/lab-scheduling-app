<?php

namespace App\Filament\Widgets;

use App\Models\JadwalFinal;
use App\Models\Semester;
use App\Enums\SemesterStatus;
use App\Filament\Resources\JadwalFinals\JadwalFinalResource; 
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;

class JadwalHariIni extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string|null
    {
        Carbon::setLocale('id');
        return 'Jadwal Praktikum Hari Ini (' . Carbon::now()->translatedFormat('l, d F Y') . ')';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JadwalFinal::query()
                    ->with(['matakuliah', 'ruangan', 'semester']) // Eager load biar ringan
                    ->whereHas('semester', function ($q) {
                        $q->where('status', SemesterStatus::Aktif);
                    })
                    ->where('hari', $this->getHariIndonesia(now()->format('l')))
                    ->orderBy('jam_mulai', 'asc')
            )
            ->columns([
                TextColumn::make('jam_mulai')
                    ->label('Jam')
                    ->formatStateUsing(fn ($record) => substr($record->jam_mulai, 0, 5) . ' - ' . substr($record->jam_selesai, 0, 5))
                    ->badge()
                    ->color('info'),

                TextColumn::make('matakuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->description(fn (JadwalFinal $record) => "{$record->matakuliah->kode_mk} - {$record->matakuliah->dosen}")
                    ->searchable(),

                TextColumn::make('matakuliah.kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('warning') 
                    ->alignCenter(),

                TextColumn::make('ruangan.nama_ruangan')
                    ->label('Ruangan')
                    ->badge()
                    ->color('success'),

                TextColumn::make('penugasanAsistenFinals_count')
                    ->counts('penugasanAsistenFinals')
                    ->label('Jml Asisten')
                    ->alignCenter(),
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Dibatalkan' => 'danger',
                        'Selesai' => 'gray',
                        default => 'primary',
                    }),
            ])
            ->actions([
                Action::make('open')
                    ->label('Detail')
                    ->icon('heroicon-m-eye')
                    ->url(fn (JadwalFinal $record): string => JadwalFinalResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }

    protected function getHariIndonesia($dayInEnglish) {
        $days = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ];

        return $days[$dayInEnglish] ?? $dayInEnglish;
    }
}