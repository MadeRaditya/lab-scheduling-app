<?php

namespace App\Livewire\Asisten;

use App\Models\AsistenSemester;
use App\Models\Semester;
use App\Enums\AsistenPeran;
use App\Enums\AsistenStatus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AsistenSemesterManager extends Component
{
    public $semesters;

    public $id_semester;
    public $status;
    public $peran;
    public $tanggal_mulai;
    public $tanggal_keluar;
    public $keterangan;

    public $editing = false;
    public $edit_id;

    public function mount()
    {
        $this->semesters = Semester::orderBy('id_semester', 'desc')->get();
        
        $this->id_semester = $this->semesters->first()->id_semester ?? null;
        $this->status = AsistenStatus::Aktif->value;
        $this->peran = AsistenPeran::Asisten->value;
    }

    public function resetForm()
    {
        $this->reset(['id_semester', 'tanggal_mulai', 'tanggal_keluar', 'keterangan', 'editing', 'edit_id']);
        $this->status = AsistenStatus::Aktif->value;
        $this->peran = AsistenPeran::Asisten->value;
    }

    public function add()
    {
        $this->validate([
            'id_semester'    => 'required|exists:semesters,id_semester',
            'status'         => 'required',
            'peran'          => 'required',
            'tanggal_mulai'  => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keterangan'     => 'nullable|string|max:500',
        ]);

        $exists = AsistenSemester::where('id_asisten', Auth::user()->id_asisten)
            ->where('id_semester', $this->id_semester)
            ->exists();

        if ($exists) {
            $this->addError('id_semester', 'Data kepengurusan untuk semester ini sudah ada.');
            return;
        }

        AsistenSemester::create([
            'id_asisten'     => Auth::user()->id_asisten,
            'id_semester'    => $this->id_semester,
            'status'         => $this->status,
            'peran'          => $this->peran,
            'tanggal_mulai'  => $this->tanggal_mulai,
            'tanggal_keluar' => $this->tanggal_keluar ?: null,
            'keterangan'     => $this->keterangan,
        ]);

        $this->resetForm();
    }

    public function edit($id)
    {
        $data = AsistenSemester::where('id_asisten', Auth::user()->id_asisten)
            ->where('id_asisten_semester', $id)
            ->firstOrFail();

        $this->editing = true;
        $this->edit_id = $id;

        $this->id_semester    = $data->id_semester;
        $this->status         = $data->status;
        $this->peran          = $data->peran;
        $this->tanggal_mulai  = $data->tanggal_mulai;
        $this->tanggal_keluar = $data->tanggal_keluar;
        $this->keterangan     = $data->keterangan;
    }

    public function update()
    {
        $this->validate([
            'id_semester'    => 'required|exists:semesters,id_semester',
            'status'         => 'required',
            'peran'          => 'required',
            'tanggal_mulai'  => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keterangan'     => 'nullable|string|max:500',
        ]);

        $data = AsistenSemester::where('id_asisten', Auth::user()->id_asisten)
            ->where('id_asisten_semester', $this->edit_id)
            ->firstOrFail();

        $data->update([
            'id_semester'    => $this->id_semester,
            'status'         => $this->status,
            'peran'          => $this->peran,
            'tanggal_mulai'  => $this->tanggal_mulai,
            'tanggal_keluar' => $this->tanggal_keluar ?: null,
            'keterangan'     => $this->keterangan,
        ]);

        $this->resetForm();
    }

    public function delete($id)
    {
        AsistenSemester::where('id_asisten', Auth::user()->id_asisten)
            ->where('id_asisten_semester', $id)
            ->delete();
    }

    public function render()
    {
        $histories = AsistenSemester::with('semester')
            ->where('id_asisten', Auth::user()->id_asisten)
            ->orderBy('id_semester', 'desc') 
            ->get();

        return view('livewire.asisten.asisten-semester-manager', [
            'histories' => $histories
        ]);
    }
}