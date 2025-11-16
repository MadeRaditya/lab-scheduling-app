<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            JamOperasionalLabSeeder::class,
        ]);

        User::factory()->create([
            "name" => "admin",
            "email" => "admin@mail.com",
            "email_verified_at" => null,
            "password" => bcrypt("admin1234"),
            "two_factor_secret" => null,
            "two_factor_recovery_codes" => null,
            "two_factor_confirmed_at" => null,
            "remember_token" => null,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
