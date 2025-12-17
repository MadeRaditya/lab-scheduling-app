<?php

namespace App\Livewire\Asisten;

use App\Models\JadwalFinal;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        $nextSchedule = JadwalFinal::query()
            ->with(['matakuliah', 'ruangan'])
            ->whereHas('penugasanAsistenFinals', fn($q) => $q->where('id_asisten', $user->id_asisten))
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai', 'asc')
            ->first();
        $totalKelas = JadwalFinal::query()
            ->whereHas('penugasanAsistenFinals', fn($q) => $q->where('id_asisten', $user->id_asisten))
            ->count();

        $totalKompetensi = $user->kompetensis()->count();

        $upcomingSchedules = JadwalFinal::query()
            ->with(['matakuliah', 'ruangan', 'semester'])
            ->whereHas('penugasanAsistenFinals', fn($q) => $q->where('id_asisten', $user->id_asisten))
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai', 'asc')
            ->take(5)
            ->get();

        return view('livewire.asisten.dashboard',[
            'user'=>$user,
            'nextSchedule'=>$nextSchedule,
            'totalKelas'=>$totalKelas,
            'totalKompetensi'=>$totalKompetensi,
            'upcomingSchedules'=>$upcomingSchedules
        ]);
    }
}
