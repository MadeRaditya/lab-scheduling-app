<?php

namespace App\Livewire\Asisten;

use App\Models\KendalaAsisten;
use App\Models\Semester;
use App\Enums\KendalaTipe;
use App\Enums\StatusKendala;
use App\Enums\JamHari;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KendalaManager extends Component
{
    public $semesters;
    public $hari_options;

    public $id_semester;
    public $tipe_kendala;
    public $hari; 
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $alasan;
    
    public $editing = false;
    public $edit_id;

    public function mount()
    {
        $this->semesters = Semester::orderBy('id_semester', 'desc')->get();
        $this->hari_options = array_column(JamHari::cases(), 'value');
        
        $this->id_semester = $this->semesters->first()->id_semester ?? null;
        $this->tipe_kendala = KendalaTipe::Lainnya->value;
    }

    public function resetForm()
    {
        $this->reset(['tipe_kendala', 'hari', 'tanggal_mulai', 'tanggal_selesai', 'alasan', 'editing', 'edit_id']);
        $this->tipe_kendala = KendalaTipe::Lainnya->value;
    }

    public function add()
    {
        $this->validate([
            'id_semester'     => 'required|exists:semesters,id_semester',
            'tipe_kendala'    => 'required',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string|max:500',
            'hari'            => 'nullable|string',
        ]);

        KendalaAsisten::create([
            'id_asisten'      => Auth::user()->id_asisten,
            'id_semester'     => $this->id_semester,
            'tipe_kendala'    => $this->tipe_kendala,
            'hari'            => $this->hari, 
            'tanggal_mulai'   => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'alasan'          => $this->alasan,
            'status'          => StatusKendala::Pending->value, 
        ]);

        $this->resetForm();
    }

    public function edit($id)
    {
        $kendala = KendalaAsisten::where('id_asisten', Auth::user()->id_asisten)
            ->where('id_kendala', $id)
            ->firstOrFail();


        $this->editing = true;
        $this->edit_id = $id;

        $this->id_semester     = $kendala->id_semester;
        $this->tipe_kendala    = $kendala->tipe_kendala;
        $this->hari            = $kendala->hari;
        $this->tanggal_mulai   = $kendala->tanggal_mulai;
        $this->tanggal_selesai = $kendala->tanggal_selesai;
        $this->alasan          = $kendala->alasan;
    }

    public function update()
    {
        $this->validate([
            'id_semester'     => 'required|exists:semesters,id_semester',
            'tipe_kendala'    => 'required',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string|max:500',
        ]);

        $kendala = KendalaAsisten::where('id_asisten', Auth::user()->id_asisten)
            ->where('id_kendala', $this->edit_id)
            ->firstOrFail();

        $kendala->update([
            'id_semester'     => $this->id_semester,
            'tipe_kendala'    => $this->tipe_kendala,
            'hari'            => $this->hari,
            'tanggal_mulai'   => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'alasan'          => $this->alasan,
        ]);

        $this->resetForm();
    }

    public function delete($id)
    {
        $kendala = KendalaAsisten::where('id_asisten', Auth::user()->id_asisten)
            ->where('id_kendala', $id)
            ->first();
            
            $kendala?->delete();
    }

    public function render()
    {
        $kendalas = KendalaAsisten::with('semester')
            ->where('id_asisten', Auth::user()->id_asisten)
            ->orderBy('id_semester', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('livewire.asisten.kendala-manager', [
            'kendalas' => $kendalas
        ]);
    }
}