<?php

namespace App\Filament\Widgets;

use App\Models\Asisten;
use App\Models\JadwalFinal;
use App\Models\Semester;
use App\Enums\SemesterStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';
    
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $semesterAktif = Semester::where('status', SemesterStatus::Aktif)->first();
        $namaSemester = $semesterAktif ? $semesterAktif->nama_semester : 'Tidak Ada Semester Aktif';
        $colorSemester = $semesterAktif ? 'success' : 'danger';

        $totalAsisten = Asisten::where('status_aktif', true)->count();
        $totalAsistenAll = Asisten::count();

        $totalJadwal = 0;
        if ($semesterAktif) {
            $totalJadwal = JadwalFinal::where('id_semester', $semesterAktif->id_semester)->count();
        }

        return [
            Stat::make('Semester Berjalan', $namaSemester)
                ->description('Semester yang sedang aktif saat ini')
                ->descriptionIcon('heroicon-m-calendar')
                ->color($colorSemester),

            Stat::make('Asisten Aktif', $totalAsisten . ' / ' . $totalAsistenAll)
                ->description('Jumlah asisten yang berstatus aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 3, 10, 5, 8, $totalAsisten]),

            Stat::make('Total Kelas Praktikum', $totalJadwal)
                ->description('Total jadwal final semester ini')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('warning'),
        ];
    }
}
