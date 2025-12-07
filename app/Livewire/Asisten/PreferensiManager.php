<?php

namespace App\Livewire\Asisten;

use App\Enums\JamHari;
use App\Enums\PreferensiTingkat;
use App\Models\JadwalAsisten;
use App\Models\PreferensiJadwalAsisten;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PreferensiManager extends Component
{
    public $semesters;
    public $hari_options;

    public $id_semester;
    public $hari;
    public $jam_mulai;
    public $jam_selesai;
    public $tingkat_preferensi;
    public $keterangan;

    public $mode = 'list';
    public $bulk_selected_slots = [];
    public $editing = false;
    public $edit_id;

    public function mount()
    {
        $this->semesters = Semester::orderBy('id_semester', 'desc')->get();
        $this->hari_options = array_column(JamHari::cases(), 'value');
        
        $this->id_semester = $this->semesters->first()->id_semester ?? null;
        
        $this->tingkat_preferensi = PreferensiTingkat::Bisa->value; 
    }

    public function getStandardSlotsProperty()
    {
        $slotsSeninKamis = [['07:30', '09:00'], ['09:00', '10:30'], ['10:30', '12:00'], ['13:00', '14:30'], ['14:40', '16:10']];
        $slotsJumat = [['07:30', '09:00'], ['09:00', '10:30'], ['10:30', '12:00'], ['13:00', '14:30'], ['14:40', '16:10'], ['16:30', '17:40'], ['17:40', '19:00'], ['19:10', '20:20']];
        $slotsSabtu = [['08:00', '09:20'], ['09:20', '10:30'], ['10:40', '11:50'], ['12:30', '13:40'], ['13:50', '15:00'], ['15:10', '16:20'], ['16:30', '17:40'], ['17:40', '19:00'], ['19:10', '20:20']];

        return [
            'Senin' => $slotsSeninKamis, 'Selasa' => $slotsSeninKamis, 'Rabu' => $slotsSeninKamis, 
            'Kamis' => $slotsSeninKamis, 'Jumat' => $slotsJumat, 'Sabtu' => $slotsSabtu
        ];
    }

    public function generateFromEmptySlots()
    {
        $this->validate(['id_semester' => 'required|exists:semesters,id_semester']);

        $busySchedules = JadwalAsisten::where('id_asisten', Auth::user()->id_asisten)
            ->where('id_semester', $this->id_semester)
            ->get();

        $busySlots = [];
        foreach ($busySchedules as $sched) {
            $start = substr($sched->jam_mulai, 0, 5);
            $end = substr($sched->jam_selesai, 0, 5);
            $busySlots[] = "{$sched->hari}-{$start}-{$end}";
        }

        $count = 0;
        foreach ($this->standardSlots as $hari => $slots) {
            foreach ($slots as $slot) {
                $start = $slot[0];
                $end = $slot[1];
                $slotKey = "{$hari}-{$start}-{$end}";

                if (!in_array($slotKey, $busySlots)) {
                    $exists = PreferensiJadwalAsisten::where('id_asisten', Auth::user()->id_asisten)
                        ->where('id_semester', $this->id_semester)
                        ->where('hari', $hari)
                        ->where('jam_mulai', $start)
                        ->exists();

                    if (!$exists) {
                        PreferensiJadwalAsisten::create([
                            'id_asisten' => Auth::user()->id_asisten,
                            'id_semester' => $this->id_semester,
                            'hari' => $hari,
                            'jam_mulai' => $start,
                            'jam_selesai' => $end,
                            'tingkat_preferensi' => PreferensiTingkat::Bisa->value,
                            'keterangan' => 'Generated automatically',
                        ]);
                        $count++;
                    }
                }
            }
        }

        $this->mode = 'list';
    }

    public function switchMode($mode)
    {
        $this->mode = $mode;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['hari', 'jam_mulai', 'jam_selesai', 'tingkat_preferensi', 'keterangan', 'editing', 'edit_id', 'bulk_selected_slots']);
        $this->tingkat_preferensi = PreferensiTingkat::Bisa->value; 
    }

    public function add()
    {
        $this->validate([
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'tingkat_preferensi' => 'required',
        ]);

        PreferensiJadwalAsisten::create([
            'id_asisten' => Auth::user()->id_asisten,
            'id_semester' => $this->id_semester,
            'hari' => $this->hari,
            'jam_mulai' => $this->jam_mulai,
            'jam_selesai' => $this->jam_selesai,
            'tingkat_preferensi' => $this->tingkat_preferensi,
            'keterangan' => $this->keterangan,
        ]);

        $this->resetForm();
        $this->mode = 'list';
    }

    public function saveBulk()
    {
        $this->validate([
            'id_semester' => 'required',
            'tingkat_preferensi' => 'required',
            'bulk_selected_slots' => 'required|array|min:1'
        ]);

        foreach ($this->bulk_selected_slots as $slotString) {
            [$hari, $mulai, $selesai] = explode('|', $slotString);
            
            PreferensiJadwalAsisten::create([
                'id_asisten' => Auth::user()->id_asisten,
                'id_semester' => $this->id_semester,
                'hari' => $hari,
                'jam_mulai' => $mulai,
                'jam_selesai' => $selesai,
                'tingkat_preferensi' => $this->tingkat_preferensi,
                'keterangan' => $this->keterangan,
            ]);
        }
        $this->resetForm();
        $this->mode = 'list';
    }

    public function delete($id)
    {
        PreferensiJadwalAsisten::where('id_asisten', Auth::user()->id_asisten)
            ->where('id_preferensi', $id)
            ->delete();
    }

    public function render()
    {
        $prefs = PreferensiJadwalAsisten::with('semester')
            ->where('id_asisten', Auth::user()->id_asisten)
            ->orderBy('id_semester', 'desc')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return view('livewire.asisten.preferensi-manager', [
            'prefs' => $prefs
        ]);
    }
}