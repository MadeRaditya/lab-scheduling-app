<?php

use App\Livewire\Asisten\JadwalSaya;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware(['auth:asisten', 'verified'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::get('/kompetensi', function () {
        return view('livewire.asisten.kompetensi');
    })->name('kompetensi');

    Route::get('/jadwalAsisten', function () {
        return view('livewire.asisten.jadwalAsisten');
    })->name('jadwalAsisten');

    Route::get('/preferensiJadwalAsisten', function () {
        return view('livewire.asisten.preferensiJadwalAsisten');
    })->name('preferensiJadwalAsisten');

    Route::get('/kendalaAsisten', function () {
        return view('livewire.asisten.kendalaAsisten');
    })->name('kendalaAsisten');

    Route::get('/asistenSemester', function () {
        return view('livewire.asisten.asistenSemester');
    })->name('asistenSemester');

    Route::get('/jadwal-saya', function () {
        return view('livewire.asisten.jadwal-saya-page');
    })->name('jadwalSaya');
});
