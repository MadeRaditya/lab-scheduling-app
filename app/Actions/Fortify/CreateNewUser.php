<?php

namespace App\Actions\Fortify;

use App\Enums\JurusanAsisten;
use App\Models\Asisten;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
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
            'nama' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'max:50'],
            'jurusan' => ['required', new Enum(JurusanAsisten::class)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(Asisten::class)],
            'no_telepon' => ['required', 'string', 'max:20'],
            'password' => $this->passwordRules(),
        ])->validate();

        return Asisten::create([
            'nama' => $input['nama'],
            'npm' => $input['npm'],
            'jurusan' => $input['jurusan'],
            'email' => $input['email'],
            'password' => bcrypt($input['password']),
            'no_telepon' => $input['no_telepon'],
            'status_aktif' => true,
        ]);
    }
}
