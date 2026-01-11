<?php

namespace App\Filament\Widgets;

use App\Enums\SemesterStatus;
use App\Models\JadwalFinal;
use App\Models\Semester;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class JadwalPerHariChart extends ChartWidget
{
    protected ?string $heading = 'Kepadatan Jadwal Praktikum';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $semesterAktif = Semester::where('status', SemesterStatus::Aktif)->first();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $dataCounts = array_fill(0, 7, 0);

        if ($semesterAktif) {
            $results = JadwalFinal::query()
                ->where('id_semester', $semesterAktif->id_semester)
                ->select('hari', DB::raw('count(*) as total'))
                ->groupBy('hari')
                ->pluck('total', 'hari')
                ->toArray();
            
            foreach ($days as $index => $day){
                if (isset($results[$day])){
                    $dataCounts[$index] = $results[$day];
                }
            }
        }
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kelas',
                    'data' => $dataCounts,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                ],
            ],
            'labels' => $days,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
