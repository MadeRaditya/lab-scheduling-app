<?php

namespace App\Services;

use App\Models\JadwalLab;
use App\Models\Asisten;
use App\Models\JadwalFinal;
use App\Models\PenugasanAsistenFinal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GreedyScheduler
{
    protected array $assignedHistory = [];

    public function generate(int $idSemester): array
    {
        DB::beginTransaction();
        $this->assignedHistory = [];

        try {
            $this->clearExistingSchedule($idSemester);

            $jadwalLabs = $this->loadJadwalLabs($idSemester);
            $asistens = $this->loadAsistens($idSemester);

            $results = [];
            $stats = [
                'total_jadwal' => $jadwalLabs->count(),
                'total_assigned' => 0,
                'total_unassigned' => 0,
                'avg_score' => 0,
            ];

            foreach ($jadwalLabs as $jadwal) {
                $result = $this->processJadwal($jadwal, $asistens);
                $results[] = $result;

                if ($result['assigned_count'] > 0) {
                    $stats['total_assigned']++;
                } else {
                    $stats['total_unassigned']++;
                }
            }

            DB::commit();

            return [
                'status' => 'success',
                'message' => "Jadwal berhasil digenerate! {$stats['total_assigned']} dari {$stats['total_jadwal']} jadwal berhasil di-assign.",
                'results' => $results,
                'stats' => $stats,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ];
        }
    }

    private function clearExistingSchedule(int $idSemester): void
    {
        $jadwalFinalIds = JadwalFinal::where('id_semester', $idSemester)->pluck('id_final');

        if ($jadwalFinalIds->isNotEmpty()) {
            PenugasanAsistenFinal::whereIn('id_jadwal_final', $jadwalFinalIds)->delete();
            JadwalFinal::where('id_semester', $idSemester)->delete();
        }
    }

    private function loadJadwalLabs(int $idSemester)
    {
        return JadwalLab::with(['matakuliah.kompetensis', 'ruangan', 'semester'])
            ->where('id_semester', $idSemester)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();
    }

    private function loadAsistens(int $idSemester)
    {
        return Asisten::with(['matakuliahs', 'kompetensis', 'preferensiJadwalAsistens', 'kendalaAsistens'])
            ->where('status_aktif', true)
            ->whereHas('asistenSemesters', function ($q) use ($idSemester) {
                $q->where('id_semester', $idSemester)->where('status', 'Aktif');
            })
            ->get();
    }

    private function processJadwal($jadwal, $asistens): array
    {
        $jadwalFinal = JadwalFinal::create([
            'id_semester' => $jadwal->id_semester,
            'matakuliah_id' => $jadwal->matakuliah_id,
            'id_ruangan' => $jadwal->id_ruangan,
            'hari' => $jadwal->hari,
            'jam_mulai' => $jadwal->jam_mulai,
            'jam_selesai' => $jadwal->jam_selesai,
            'metode_penjadwalan' => 'Greedy Algorithm (Smart Match)',
            'status' => 'Aktif',
        ]);

        $ranked = $this->rankAsistensForJadwal($jadwal, $asistens, false);

        if ($ranked->isEmpty()) {
            $ranked = $this->rankAsistensForJadwal($jadwal, $asistens, true);
            if ($ranked->isNotEmpty()) {
                $jadwalFinal->update(['catatan' => 'Panic Mode: Mengabaikan kendala pribadi.']);
            }
        }

        $needed = $jadwal->jumlah_asisten_dibutuhkan ?? 1;
        $top = $ranked->take($needed);

        $assignedAsistens = [];
        foreach ($top as $rank) {
            $this->assignAsisten($jadwalFinal, $rank);
            $assignedAsistens[] = [
                'nama' => $rank['asisten']->nama,
                'score' => $rank['score'],
            ];
        }

        $assignedCount = $top->count();

        if ($assignedCount == 0) {
            $jadwalFinal->update([
                'status' => 'Dibatalkan',
                'catatan' => 'GAGAL: Tidak ada asisten dengan verifikasi ATAU skill yang cocok.',
            ]);
        } elseif ($assignedCount < $needed) {
            $jadwalFinal->update([
                'catatan' => $jadwalFinal->catatan . " (Kurang Asisten: Dapat $assignedCount dari $needed).",
            ]);
        }

        return [
            'mk' => $jadwal->matakuliah->nama_mk . " ({$jadwal->matakuliah->kelas})",
            'hari' => $jadwal->hari,
            'waktu' => "{$jadwal->jam_mulai} - {$jadwal->jam_selesai}",
            'needed' => $needed,
            'assigned_count' => $assignedCount,
            'asistens' => $assignedAsistens,
        ];
    }

    private function assignAsisten($jadwalFinal, $rank): void
    {
        $asistenId = $rank['asisten']->id_asisten;

        PenugasanAsistenFinal::create([
            'id_jadwal_final' => $jadwalFinal->id_final,
            'id_asisten' => $asistenId,
            'id_kompetensi' => null,
            'skor_kecocokan' => $rank['score'],
            'peran' => 'Asisten Utama',
            'status_kehadiran' => 'Terjadwal',
        ]);

        $this->markAsistenAsBusy(
            $asistenId,
            $jadwalFinal->hari,
            $jadwalFinal->jam_mulai,
            $jadwalFinal->jam_selesai
        );
    }

    private function rankAsistensForJadwal($jadwal, $asistens, bool $ignorePersonalConflicts = false)
    {
        $ranked = collect();
        $namaMkTarget = $jadwal->matakuliah->nama_mk;

        $requiredSkillIds = $jadwal->matakuliah->kompetensis->pluck('id_kompetensi')->toArray();

        foreach ($asistens as $asisten) {
            $isStrictVerified = $asisten->matakuliahs->contains('id', $jadwal->matakuliah_id);

            $isSoftVerified = false;
            if (!$isStrictVerified) {
                $isSoftVerified = $asisten->matakuliahs->contains('nama_mk', $namaMkTarget);
            }

            $isSkillMatched = false;
            if (!$isStrictVerified && !$isSoftVerified && !empty($requiredSkillIds)) {
                $isSkillMatched = $asisten->kompetensis->contains(function ($k) use ($requiredSkillIds) {
                    return in_array($k->id_kompetensi, $requiredSkillIds);
                });
            }

            if (!$isStrictVerified && !$isSoftVerified && !$isSkillMatched) {
                continue;
            }

            if ($this->isAsistenBusy($asisten->id_asisten, $jadwal->hari, $jadwal->jam_mulai, $jadwal->jam_selesai)) {
                continue;
            }

            if (!$ignorePersonalConflicts && $this->hasPersonalConflict($asisten, $jadwal)) {
                continue;
            }

            $score = 50;

            if ($isStrictVerified) {
                $score += 20;
            } elseif ($isSoftVerified) {
                $score += 15;
            } elseif ($isSkillMatched) {
                $score += 5;
            }

            $prefScore = $this->calculatePreferenceScore($asisten, $jadwal);
            $score += $prefScore;

            if ($ignorePersonalConflicts) {
                $score -= 15;
            }

            $ranked->push([
                'asisten' => $asisten,
                'score' => $score,
            ]);
        }

        return $ranked->sortByDesc('score')->values();
    }

    private function calculatePreferenceScore($asisten, $jadwal): int
    {
        $pref = $asisten->preferensiJadwalAsistens
            ->where('hari', $jadwal->hari)
            ->where('id_semester', $jadwal->id_semester)
            ->filter(function ($p) use ($jadwal) {
                return $this->checkTimeOverlap(
                    $p->jam_mulai,
                    $p->jam_selesai,
                    $jadwal->jam_mulai,
                    $jadwal->jam_selesai
                );
            })
            ->first();

        if (!$pref) {
            return 0;
        }

        return match ($pref->tingkat_preferensi) {
            'Sangat Suka' => 30,
            'Bisa' => 15,
            'Kurang Suka' => 5,
            default => 0,
        };
    }

    private function checkTimeOverlap($start1, $end1, $start2, $end2): bool
    {
        return ($start1 < $end2) && ($end1 > $start2);
    }

    private function hasPersonalConflict($asisten, $jadwal): bool
    {
        $today = Carbon::today();

        foreach ($asisten->kendalaAsistens as $kendala) {
            if ($kendala->hari != $jadwal->hari || $kendala->status != 'Aktif') {
                continue;
            }

            if ($kendala->tanggal_mulai && $kendala->tanggal_selesai) {
                $startDate = Carbon::parse($kendala->tanggal_mulai);
                $endDate = Carbon::parse($kendala->tanggal_selesai);

                if ($today->between($startDate, $endDate)) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

    private function markAsistenAsBusy($asistenId, $hari, $start, $end): void
    {
        $this->assignedHistory[$asistenId][] = [
            'hari' => $hari,
            'start' => $start,
            'end' => $end
        ];
    }

    private function isAsistenBusy($asistenId, $hari, $start, $end): bool
    {
        if (!isset($this->assignedHistory[$asistenId])) {
            return false;
        }

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
