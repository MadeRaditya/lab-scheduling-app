<?php

namespace App\Services;

use App\Models\JadwalLab;
use App\Models\Asisten;
use App\Models\JadwalFinal;
use App\Models\PenugasanAsistenFinal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GreedyScheduler
{
    protected array $assignedHistory = [];

    public function generate(int $idSemester): array
    {
        DB::beginTransaction();
        $this->assignedHistory = []; 

        try {
            $jadwalFinalIds = JadwalFinal::where('id_semester', $idSemester)->pluck('id_final');
            PenugasanAsistenFinal::whereIn('id_jadwal_final', $jadwalFinalIds)->delete();
            JadwalFinal::where('id_semester', $idSemester)->delete();

            $jadwalLabs = JadwalLab::with(['matakuliah.kompetensis', 'ruangan', 'semester'])
                ->where('id_semester', $idSemester)
                ->get();

            $asistens = Asisten::with(['kompetensis', 'preferensiJadwalAsistens', 'kendalaAsistens'])
                ->whereHas('asistenSemesters', function ($q) use ($idSemester) {
                    $q->where('id_semester', $idSemester)->where('status', 'Aktif');
                })
                ->get();

            $results = [];

            foreach ($jadwalLabs as $jadwal) {
                $jadwalFinal = JadwalFinal::create([
                    'id_semester' => $jadwal->id_semester,
                    'kode_mk' => $jadwal->kode_mk,
                    'id_ruangan' => $jadwal->id_ruangan,
                    'hari' => $jadwal->hari,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'metode_penjadwalan' => 'Greedy Algorithm',
                    'status' => 'Aktif',
                ]);

                $requiredSkills = $jadwal->matakuliah->kompetensis;
                
                $ranked = $this->rankAsistensForJadwal(
                    $jadwal,
                    $requiredSkills,
                    $asistens
                );

                $needed = $jadwal->jumlah_asisten_dibutuhkan ?? 1;
                $top = $ranked->take($needed);

                foreach ($top as $rank) {
                    $asistenId = $rank['asisten']->id_asisten;

                    PenugasanAsistenFinal::create([
                        'id_jadwal_final' => $jadwalFinal->id_final,
                        'id_asisten' => $asistenId,
                        'id_kompetensi' => $rank['kompetensi'] ?? null,
                        'skor_kecocokan' => $rank['score'],
                        'peran' => 'Asisten Utama',
                        'status_kehadiran' => 'Terjadwal',
                    ]);

                    $this->markAsistenAsBusy($asistenId, $jadwal->hari, $jadwal->jam_mulai, $jadwal->jam_selesai);
                }

                $results[] = [
                    'mk' => $jadwal->matakuliah->nama_mk,
                    'asisten' => $top->map(fn($t) => $t['asisten']->nama),
                ];
            }

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Jadwal berhasil digenerate!',
                'results' => $results
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage() . ' Line: ' . $e->getLine()
            ];
        }
    }

    private function rankAsistensForJadwal($jadwal, $requiredSkills, $asistens)
    {
        $ranked = collect();

        foreach ($asistens as $asisten) {
            if ($this->hasPersonalConflict($asisten, $jadwal)) {
                continue; 
            }

            if ($this->isAsistenBusy($asisten->id_asisten, $jadwal->hari, $jadwal->jam_mulai, $jadwal->jam_selesai)) {
                continue;
            }

            $score = 0;
            $bestSkill = null;

            foreach ($asisten->kompetensis as $ak) {
                foreach ($requiredSkills as $rk) {
                    if ($ak->id_kompetensi === $rk->id_kompetensi) {
                        $score += 50;
                        $bestSkill = $rk->id_kompetensi;
                    }
                }
            }

            $pref = $asisten->preferensiJadwalAsistens
                ->where('hari', $jadwal->hari)
                ->filter(function ($p) use ($jadwal) {
                     return $this->checkTimeOverlap($p->jam_mulai, $p->jam_selesai, $jadwal->jam_mulai, $jadwal->jam_selesai);
                })
                ->first();

            if ($pref) {
                $score += match($pref->tingkat_preferensi) {
                    'Sangat Suka' => 30,
                    'Bisa' => 20,
                    'Kurang Suka' => 5,
                    default => 0,
                };
            }

            $ranked->push([
                'asisten' => $asisten,
                'score' => $score,
                'kompetensi' => $bestSkill,
            ]);
        }

        return $ranked->sortByDesc('score')->values();
    }

    private function checkTimeOverlap($start1, $end1, $start2, $end2)
    {
        return ($start1 < $end2) && ($end1 > $start2);
    }

    private function hasPersonalConflict($asisten, $jadwal)
    {
        foreach ($asisten->kendalaAsistens as $kendala) {
            if ($kendala->hari == $jadwal->hari && $kendala->status == 'Aktif') {
                 if (!$kendala->tanggal_mulai || !$kendala->tanggal_selesai) return true;

                 if ($this->checkTimeOverlap($kendala->tanggal_mulai, $kendala->tanggal_selesai, $jadwal->jam_mulai, $jadwal->jam_selesai)) {
                     return true;
                 }
            }
        }
        return false;
    }

    private function markAsistenAsBusy($asistenId, $hari, $start, $end)
    {
        $this->assignedHistory[$asistenId][] = [
            'hari' => $hari,
            'start' => $start,
            'end' => $end
        ];
    }

    private function isAsistenBusy($asistenId, $hari, $start, $end)
    {
        if (!isset($this->assignedHistory[$asistenId])) return false;

        foreach ($this->assignedHistory[$asistenId] as $slot) {
            if ($slot['hari'] == $hari) {
                if ($this->checkTimeOverlap($slot['start'], $slot['end'], $start, $end)) {
                    return true;
                }
            }
        }
        return false;
    }
}