<?php

namespace App\Livewire\Asisten;

use App\Enums\JamHari;
use App\Models\JadwalAsisten;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class JadwalManager extends Component
{
    public $semesters; 
    public $hari_options;

    public $id_semester;
    public $hari;
    public $jam_mulai;
    public $jam_selesai;
    public $jenis_kegiatan;
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
    }

    public function getStandardSlotsProperty()
    {
        $slotsSeninKamis = [
            ['07:30', '09:00'], ['09:00', '10:30'], ['10:30', '12:00'],
            ['13:00', '14:30'], ['14:40', '16:10']
        ];
        $slotsJumat = [
            ['07:30', '09:00'], ['09:00', '10:30'], ['10:30', '12:00'],
            ['13:00', '14:30'], ['14:40', '16:10'], ['16:30', '17:40'],
            ['17:40', '19:00'], ['19:10', '20:20']
        ];

        $slotsSabtu = [
            ['08:00', '09:20'], ['09:20', '10:30'], ['10:40', '11:50'],
            ['12:30', '13:40'], ['13:50', '15:00'], ['15:10', '16:20'],
            ['16:30', '17:40'], ['17:40', '19:00'], ['19:10', '20:20']
        ];

        return [
            'Senin'  => $slotsSeninKamis,
            'Selasa' => $slotsSeninKamis,
            'Rabu'   => $slotsSeninKamis,
            'Kamis'  => $slotsSeninKamis,
            'Jumat'  => $slotsJumat,
            'Sabtu'  => $slotsSabtu,
        ];
    }

    public function switchMode($mode)
    {
        $this->mode = $mode;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['hari', 'jam_mulai', 'jam_selesai', 'jenis_kegiatan', 'keterangan', 'editing', 'edit_id', 'bulk_selected_slots']);
    }

    public function add()
    {
        $this->validate([
            'id_semester'    => 'required|exists:semesters,id_semester',
            'hari'           => 'required|string',
            'jam_mulai'      => 'required',
            'jam_selesai'    => 'required|after:jam_mulai',
            'jenis_kegiatan' => 'required|string|max:255',
            'keterangan'     => 'nullable|string',
        ]);

        JadwalAsisten::create([
            'id_asisten'     => Auth::user()->id_asisten, 
            'id_semester'    => $this->id_semester,
            'hari'           => $this->hari,
            'jam_mulai'      => $this->jam_mulai,
            'jam_selesai'    => $this->jam_selesai,
            'jenis_kegiatan' => $this->jenis_kegiatan,
            'keterangan'     => $this->keterangan,
        ]);

        $this->resetForm();
        $this->mode = 'list';
    }

    public function saveBulk()
    {
        $this->validate([
            'id_semester'               => 'required|exists:semesters,id_semester',
            'jenis_kegiatan'            => 'required|string|max:255',
            'bulk_selected_slots'       => 'required|array|min:1',
        ],[
            'bulk_selected_slots.required'=> 'Pilih setidaknya satu jadwal jam.',
        ]);
        $count = 0;
        foreach ($this->bulk_selected_slots as $slotString){
            [$hari, $mulai, $selesai] = explode('|', $slotString);
            JadwalAsisten::create([
                'id_asisten'     => Auth::user()->id_asisten,
                'id_semester'    => $this->id_semester,
                'hari'           => $hari,
                'jam_mulai'      => $mulai,
                'jam_selesai'    => $selesai,
                'jenis_kegiatan' => $this->jenis_kegiatan,
                'keterangan'     => $this->keterangan,
            ]);
            $count++;
        }
        $this->resetForm();
        $this->mode = 'list';
    }



    public function edit($id)
    {
        $jadwal = JadwalAsisten::where('id_asisten', Auth::user()->id_asisten)
            ->where('id_jadwal_asisten', $id)
            ->firstOrFail();

        $this->editing = true;
        $this->edit_id = $id;
        $this->mode = 'single';

        $this->id_semester    = $jadwal->id_semester;
        $this->hari           = $jadwal->hari;
        $this->jam_mulai      = $jadwal->jam_mulai;
        $this->jam_selesai    = $jadwal->jam_selesai;
        $this->jenis_kegiatan = $jadwal->jenis_kegiatan;
        $this->keterangan     = $jadwal->keterangan;
    }

    public function update()
    {
        $this->validate([
            'id_semester'    => 'required|exists:semesters,id_semester',
            'hari'           => 'required|string',
            'jam_mulai'      => 'required',
            'jam_selesai'    => 'required|after:jam_mulai',
            'jenis_kegiatan' => 'required|string|max:255',
        ]);

        $jadwal = JadwalAsisten::where('id_asisten', Auth::user()->id_asisten)
            ->where('id_jadwal_asisten', $this->edit_id)
            ->firstOrFail();

        $jadwal->update([
            'id_semester'    => $this->id_semester,
            'hari'           => $this->hari,
            'jam_mulai'      => $this->jam_mulai,
            'jam_selesai'    => $this->jam_selesai,
            'jenis_kegiatan' => $this->jenis_kegiatan,
            'keterangan'     => $this->keterangan,
        ]);

        $this->resetForm();
        $this->mode = 'list';
    }

    public function delete($id)
    {
        JadwalAsisten::where('id_asisten', Auth::user()->id_asisten)
            ->where('id_jadwal_asisten', $id)
            ->delete();
    }

    public function render()
    {
        $jadwals = JadwalAsisten::with('semester')
            ->where('id_asisten', Auth::user()->id_asisten)
            ->orderBy('id_semester', 'desc') 
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return view('livewire.asisten.jadwal-manager', [
            'jadwals' => $jadwals
        ]);
    }
}