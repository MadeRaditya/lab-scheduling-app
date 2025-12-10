<?php

namespace App\Livewire\Asisten;

use App\Models\JadwalFinal;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;    
use Livewire\Component;

#[Title('Jadwal Saya')] 
class JadwalSaya extends Component
{
    public function render()
    {
        $jadwals = JadwalFinal::query()
            ->with([
                'semester', 
                'matakuliah', 
                'ruangan', 
                'penugasanAsistenFinals.asisten' 
            ])
            ->whereHas('penugasanAsistenFinals', function ($query) {
                $query->where('id_asisten', Auth::user()->id_asisten);
            })
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return view('livewire.asisten.jadwal-saya', [
            'jadwals' => $jadwals
        ]);
    }
}