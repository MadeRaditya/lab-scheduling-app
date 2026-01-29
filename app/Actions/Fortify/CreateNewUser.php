<?php

namespace App\Actions\Fortify;

use App\Enums\JurusanAsisten;
use App\Models\Asisten;
use App\Models\CalonAsisten;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): Asisten
    {
        Validator::make($input, [
            'token_registrasi' => ['required', 'string'],
            'nama' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'max:50'],
            'jurusan' => ['required', new Enum(JurusanAsisten::class)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(Asisten::class)],
            'no_telepon' => ['required', 'string', 'max:20'],
            'password' => $this->passwordRules(),
        ])->validate();

        $calon = CalonAsisten::where('token', $input['token_registrasi'])->first();

        if (!$calon) {
            throw ValidationException::withMessages([
                'token_registrasi' => ['Token registrasi tidak valid.'],
            ]);
        }

        if ($calon->is_registered) {
            throw ValidationException::withMessages([
                'token_registrasi' => ['Token ini sudah digunakan.'],
            ]);
        }

        if ($calon->npm !== $input['npm']) {
            throw ValidationException::withMessages([
                'npm' => ['NPM tidak sesuai dengan data token yang dimasukkan.'],
            ]);
        }

        $calon->update(['is_registered' => true]);

        return Asisten::create([
            'nama' => $input['nama'],
            'npm' => $input['npm'],
            'jurusan' => $input['jurusan'],
            'email' => $input['email'],
            'password' => bcrypt($input['password']),
            'no_telepon' => $input['no_telepon'],
            'status_aktif' => true,
            'tanggal_daftar' => now(),
        ]);
    }
}
