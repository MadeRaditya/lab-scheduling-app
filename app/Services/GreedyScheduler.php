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

            Log::info("=== Greedy Scheduler Started ===");
            Log::info("Semester ID: {$idSemester}");
            Log::info("Total jadwal labs: " . $jadwalLabs->count());
            Log::info("Total asistens aktif: " . $asistens->count());

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
                    Log::warning("⚠️ Tidak ada asisten yang bisa di-assign untuk {$result['mk']}");
                }
            }

            DB::commit();

            Log::info("=== Greedy Scheduler Completed ===");
            Log::info("Assigned: {$stats['total_assigned']}/{$stats['total_jadwal']}");
            Log::info("Unassigned: {$stats['total_unassigned']}");

            return [
                'status' => 'success',
                'message' => "Jadwal berhasil digenerate! {$stats['total_assigned']} dari {$stats['total_jadwal']} jadwal berhasil di-assign.",
                'results' => $results,
                'stats' => $stats,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Error in GreedyScheduler: " . $e->getMessage());
            Log::error($e->getTraceAsString());

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
            Log::info("Cleared existing schedule");
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
        return Asisten::with(['kompetensis', 'preferensiJadwalAsistens', 'kendalaAsistens'])
            ->whereHas('asistenSemesters', function ($q) use ($idSemester) {
                $q->where('id_semester', $idSemester)->where('status', 'Aktif');
            })
            ->get();
    }

    private function processJadwal($jadwal, $asistens): array
    {
        Log::info("Processing: {$jadwal->matakuliah->nama_mk} - {$jadwal->hari} {$jadwal->jam_mulai}-{$jadwal->jam_selesai}");

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
        
        $ranked = $this->rankAsistensForJadwal($jadwal, $requiredSkills, $asistens);

        $needed = $jadwal->jumlah_asisten_dibutuhkan ?? 1;
        $top = $ranked->take($needed);

        Log::info("Needed: {$needed}, Available: {$top->count()}");

        $assignedAsistens = [];
        foreach ($top as $rank) {
            $this->assignAsisten($jadwalFinal, $rank);
            $assignedAsistens[] = [
                'nama' => $rank['asisten']->nama,
                'score' => $rank['score'],
            ];
        }

        return [
            'mk' => $jadwal->matakuliah->nama_mk,
            'hari' => $jadwal->hari,
            'waktu' => "{$jadwal->jam_mulai} - {$jadwal->jam_selesai}",
            'needed' => $needed,
            'assigned_count' => $top->count(),
            'asistens' => $assignedAsistens,
        ];
    }

    
    private function assignAsisten($jadwalFinal, $rank): void
    {
        $asistenId = $rank['asisten']->id_asisten;

        PenugasanAsistenFinal::create([
            'id_jadwal_final' => $jadwalFinal->id_final,
            'id_asisten' => $asistenId,
            'id_kompetensi' => $rank['kompetensi'] ?? null,
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

        Log::info("✅ Assigned: {$rank['asisten']->nama} (Score: {$rank['score']})");
    }

    private function rankAsistensForJadwal($jadwal, $requiredSkills, $asistens)
    {
        $ranked = collect();

        foreach ($asistens as $asisten) {
            if ($this->hasPersonalConflict($asisten, $jadwal)) {
                Log::debug("  ⏩ {$asisten->nama}: Skipped (Personal conflict)");
                continue;
            }

            if ($this->isAsistenBusy($asisten->id_asisten, $jadwal->hari, $jadwal->jam_mulai, $jadwal->jam_selesai)) {
                Log::debug("  ⏩ {$asisten->nama}: Skipped (Already busy)");
                continue;
            }

            $score = 0;
            $bestSkill = null;

            $hasMatchingSkill = false;
            foreach ($asisten->kompetensis as $ak) {
                foreach ($requiredSkills as $rk) {
                    if ($ak->id_kompetensi == $rk->id_kompetensi) {
                        $score += 60;
                        $bestSkill = $rk->id_kompetensi;
                        $hasMatchingSkill = true;
                        Log::debug("  ✓ {$asisten->nama}: Kompetensi match (+60)");
                        break 2;
                    }
                }
            }

            if (!$hasMatchingSkill) {
                Log::debug("  ⚠️ {$asisten->nama}: No matching kompetensi");
            }

            $prefScore = $this->calculatePreferenceScore($asisten, $jadwal);
            $score += $prefScore;

            $score += 10;

            Log::debug("  📊 {$asisten->nama}: Final Score = {$score}");

            $ranked->push([
                'asisten' => $asisten,
                'score' => $score,
                'kompetensi' => $bestSkill,
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
            Log::debug("    ⚠️ No preferensi match");
            return 0;
        }

        $score = match ($pref->tingkat_preferensi) {
            'Sangat Suka' => 30,
            'Bisa' => 20,
            'Kurang Suka' => 10,
            default => 0,
        };

        Log::debug("    ✓ Preferensi: {$pref->tingkat_preferensi} (+{$score})");
        return $score;
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
                    Log::debug("    🚫 Kendala aktif: {$kendala->jenis_kendala}");
                    return true;
                }
            } else {
                Log::debug("    🚫 Kendala permanen: {$kendala->jenis_kendala}");
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