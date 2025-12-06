<?php

namespace App\Livewire\Asisten;

use App\Models\Kompetensi;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KompetensiManager extends Component
{
    public $allKompetensi;

    public $selected_kompetensi;
    public $tingkat_penguasaan;
    public $tanggal_sertifikasi;
    public $sertifikasi;

    public $editing = false;
    public $edit_pivot_id;

    public function mount()
    {
        $this->allKompetensi = Kompetensi::all();
    }

    public function resetForm()
    {
        $this->reset(['selected_kompetensi', 'tingkat_penguasaan', 'tanggal_sertifikasi', 'sertifikasi', 'editing', 'edit_pivot_id']);
    }

    public function add()
    {
        $this->validate([
            'selected_kompetensi' => 'required',
            'tingkat_penguasaan' => 'required',
        ]);

        Auth::user()->kompetensis()->attach($this->selected_kompetensi, [
            'tingkat_penguasaan' => $this->tingkat_penguasaan,
            'tanggal_sertifikasi' => $this->tanggal_sertifikasi ?: null,
            'sertifikasi' => $this->sertifikasi ?: null,
        ]);

        $this->resetForm();
    }

    public function edit($kompetensiId)
    {
        $asisten = Auth::user();
        $kompetensi = $asisten->kompetensis()->where('kompetensis.id_kompetensi', $kompetensiId)->first();

        if ($kompetensi) {
            $this->editing = true;
            $this->edit_pivot_id = $kompetensiId;

            $this->selected_kompetensi = $kompetensiId;
            $this->tingkat_penguasaan = $kompetensi->pivot->tingkat_penguasaan;
            $this->tanggal_sertifikasi = $kompetensi->pivot->tanggal_sertifikasi;
            $this->sertifikasi = $kompetensi->pivot->sertifikasi;
        }
    }

    public function update()
    {
        Auth::user()->kompetensis()->updateExistingPivot($this->edit_pivot_id, [
            'tingkat_penguasaan' => $this->tingkat_penguasaan,
            'tanggal_sertifikasi' => $this->tanggal_sertifikasi ?: null,
            'sertifikasi' => $this->sertifikasi ?: null,
        ]);

        $this->resetForm();
    }

    public function delete($kompetensiId)
    {
        Auth::user()->kompetensis()->detach($kompetensiId);
    }

    public function render()
    {
        $kompetensis = Auth::user()
            ->kompetensis()
            ->withPivot('tingkat_penguasaan', 'tanggal_sertifikasi', 'sertifikasi')
            ->get();
        return view('livewire.asisten.kompetensi-manager', [
            'kompetensis' => $kompetensis,
        ]);
    }
}
