<?php

namespace App\Filament\Widgets;

use App\Models\JadwalFinal;
use App\Models\Semester;
use App\Enums\SemesterStatus;
use App\Filament\Resources\JadwalLabs\JadwalLabResource;
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
                    ->description(fn ($record) => $record->matakuliah->kode_mk)
                    ->searchable(),

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
            ])
            ->actions([
                Action::make('open')
                ->url(fn (JadwalFinal $record): string => JadwalLabResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-eye')
                    ->label('Detail'),
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
