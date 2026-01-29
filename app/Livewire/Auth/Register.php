<?php

namespace App\Livewire\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\Models\CalonAsisten;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Register extends Component
{
    public $token_registrasi;
    public $nama;
    public $npm;
    public $jurusan;
    public $email;
    public $no_telepon;
    public $password;
    public $password_confirmation;

    public $tokenValid = false;

    public function cekToken()
    {
        $this->validate(['token_registrasi' => 'required|min:5']);

        $calon = CalonAsisten::where('token', $this->token_registrasi)->first();

        if (!$calon) {
            $this->addError('token_registrasi', 'Token tidak ditemukan.');
            return;
        }

        if ($calon->is_registered) {
            $this->addError('token_registrasi', 'Token sudah terpakai.');
            return;
        }

        $this->tokenValid = true;
        $this->nama = $calon->nama;
        $this->npm = $calon->npm;
    }

    public function register(CreateNewUser $creator)
    {
        $user = $creator->create([
            'token_registrasi' => $this->token_registrasi,
            'nama' => $this->nama,
            'npm' => $this->npm,
            'jurusan' => $this->jurusan,
            'email' => $this->email,
            'no_telepon' => $this->no_telepon,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        event(new Registered($user));
        Auth::guard('asisten')->login($user);

        return redirect()->route('dashboard');
    }
    public function render()
    {
        return view('livewire.auth.register')->layout('components.layouts.auth');
    }
}
