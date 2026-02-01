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
    protected array $assignmentCounts = [];
    protected array $previousSessionAssignments = [];

    public function generate(int $idSemester): array
    {
        DB::beginTransaction();
        $this->assignedHistory = [];
        $this->assignmentCounts = [];
        $this->previousSessionAssignments = [];

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
            ->orderBy('id_ruangan')
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

        $needed = $jadwal->jumlah_asisten_dibutuhkan ?? 1;
        $assignedAsistens = [];
        $assignedCount = 0;

        $chainKey = $this->getChainKey($jadwal);
        $previousAssistants = $this->previousSessionAssignments[$chainKey] ?? null;

        if ($previousAssistants) {
            $jadwalFinal->update(['catatan' => 'Sesi Lanjutan (4 SKS): Menggunakan asisten sesi sebelumnya.']);

            foreach ($previousAssistants as $prevData) {
                $asisten = $asistens->firstWhere('id_asisten', $prevData['id']);
                if ($asisten) {
                    $this->assignAsisten($jadwalFinal, $asisten, $prevData['score']);
                    $assignedAsistens[] = ['nama' => $asisten->nama, 'score' => $prevData['score']];
                    $assignedCount++;
                }
            }
        }

        if ($assignedCount < $needed) {
            $ranked = $this->rankAsistensForJadwal($jadwal, $asistens, false);

            if ($ranked->isEmpty()) {
                $ranked = $this->rankAsistensForJadwal($jadwal, $asistens, true);
                if ($ranked->isNotEmpty()) {
                    $jadwalFinal->update(['catatan' => 'Panic Mode: Mengabaikan kendala pribadi.']);
                }
            }

            $remainingNeeded = $needed - $assignedCount;
            $top = $ranked->take($remainingNeeded);

            foreach ($top as $rank) {
                $this->assignAsisten($jadwalFinal, $rank['asisten'], $rank['display_score']);
                $assignedAsistens[] = [
                    'nama' => $rank['asisten']->nama,
                    'score' => $rank['display_score'],
                ];
                $assignedCount++;
            }
        }

        $currentAssignedData = collect($assignedAsistens)->map(function ($a) use ($asistens) {
            return [
                'id' => $asistens->firstWhere('nama', $a['nama'])->id_asisten,
                'score' => $a['score']
            ];
        })->toArray();

        $this->previousSessionAssignments[$chainKey] = $currentAssignedData;

        if ($assignedCount == 0) {
            $jadwalFinal->update(['status' => 'Dibatalkan', 'catatan' => 'GAGAL: Tidak ada asisten yang cocok.']);
        } elseif ($assignedCount < $needed) {
            $jadwalFinal->update(['catatan' => $jadwalFinal->catatan . " (Kurang Asisten: Dapat $assignedCount dari $needed)."]);
        }

        return [
            'mk' => $jadwal->matakuliah->nama_mk,
            'hari' => $jadwal->hari,
            'waktu' => "{$jadwal->jam_mulai} - {$jadwal->jam_selesai}",
            'needed' => $needed,
            'assigned_count' => $assignedCount,
            'asistens' => $assignedAsistens,
        ];
    }

    private function getChainKey($jadwal): string
    {
        return "{$jadwal->hari}-{$jadwal->id_ruangan}-{$jadwal->matakuliah_id}-{$jadwal->matakuliah->kelas}";
    }

    private function assignAsisten($jadwalFinal, $asisten, $score): void
    {
        $dbScore = min(100, max(0, $score));

        PenugasanAsistenFinal::create([
            'id_jadwal_final' => $jadwalFinal->id_final,
            'id_asisten' => $asisten->id_asisten,
            'id_kompetensi' => null,
            'skor_kecocokan' => $dbScore,
            'peran' => 'Asisten Utama',
            'status_kehadiran' => 'Terjadwal',
        ]);

        $this->markAsistenAsBusy(
            $asisten->id_asisten,
            $jadwalFinal->hari,
            $jadwalFinal->jam_mulai,
            $jadwalFinal->jam_selesai
        );

        $this->assignmentCounts[$asisten->id_asisten] =
            ($this->assignmentCounts[$asisten->id_asisten] ?? 0) + 1;
    }

    private function rankAsistensForJadwal($jadwal, $asistens, bool $ignorePersonalConflicts = false)
    {
        $ranked = collect();
        $namaMkTarget = $jadwal->matakuliah->nama_mk;
        $requiredSkillIds = $jadwal->matakuliah->kompetensis->pluck('id_kompetensi')->toArray();

        foreach ($asistens as $asisten) {
            $isStrictVerified = $asisten->matakuliahs->contains('id', $jadwal->matakuliah_id);
            $isSoftVerified = !$isStrictVerified &&
                $asisten->matakuliahs->contains('nama_mk', $namaMkTarget);

            $isSkillMatched = !$isStrictVerified && !$isSoftVerified && !empty($requiredSkillIds) &&
                $asisten->kompetensis->contains(fn($k) => in_array($k->id_kompetensi, $requiredSkillIds));

            if (!$isStrictVerified && !$isSoftVerified && !$isSkillMatched) continue;
            if ($this->isAsistenBusy($asisten->id_asisten, $jadwal->hari, $jadwal->jam_mulai, $jadwal->jam_selesai)) continue;
            if (!$ignorePersonalConflicts && $this->hasPersonalConflict($asisten, $jadwal)) continue;

            $baseScore = 20;

            if ($isStrictVerified) $baseScore += 60;
            elseif ($isSoftVerified) $baseScore += 45;
            elseif ($isSkillMatched) $baseScore += 30;

            $prefScore = $this->calculatePreferenceScore($asisten, $jadwal);
            if ($prefScore == 0 && !$ignorePersonalConflicts) continue;

            $competencyScore = $baseScore + $prefScore;

            $angkatan = (int) substr($asisten->npm, 0, 2);
            $yearsActive = max(0, (int) date('y') - $angkatan);
            $competencyScore += $yearsActive * 2;

            if ($ignorePersonalConflicts) $competencyScore -= 15;

            $sortingScore = $competencyScore;
            $currentLoad = $this->assignmentCounts[$asisten->id_asisten] ?? 0;

            if ($currentLoad >= 16) $sortingScore -= 1000;
            elseif ($currentLoad >= 12) $sortingScore -= 50;
            elseif ($currentLoad >= 8) $sortingScore -= 20;
            elseif ($currentLoad >= 4) $sortingScore -= 5;

            $finalDisplayScore = $competencyScore - ($currentLoad * 0.5);

            $ranked->push([
                'asisten' => $asisten,
                'sorting_score' => $sortingScore,
                'display_score' => $finalDisplayScore,
            ]);
        }

        return $ranked->sortByDesc('sorting_score')->values();
    }

    private function calculatePreferenceScore($asisten, $jadwal): int
    {
        $pref = $asisten->preferensiJadwalAsistens
            ->where('hari', $jadwal->hari)
            ->where('id_semester', $jadwal->id_semester)
            ->filter(fn($p) => $this->checkTimeOverlap(
                $p->jam_mulai,
                $p->jam_selesai,
                $jadwal->jam_mulai,
                $jadwal->jam_selesai
            ))
            ->first();

        if (!$pref) return 0;

        return match ($pref->tingkat_preferensi) {
            'Prioritas Utama' => 20,
            'Bersedia' => 15,
            'Cadangan' => 5,
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
            if ($kendala->hari != $jadwal->hari || $kendala->status != 'Aktif') continue;

            if ($kendala->tanggal_mulai && $kendala->tanggal_selesai) {
                if ($today->between(
                    Carbon::parse($kendala->tanggal_mulai),
                    Carbon::parse($kendala->tanggal_selesai)
                )) return true;
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
        if (!isset($this->assignedHistory[$asistenId])) return false;

        foreach ($this->assignedHistory[$asistenId] as $slot) {
            if ($slot['hari'] == $hari &&
                $this->checkTimeOverlap($slot['start'], $slot['end'], $start, $end)) {
                return true;
            }
        }

        return false;
    }
}
