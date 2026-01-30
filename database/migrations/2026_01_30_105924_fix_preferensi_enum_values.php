<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('preferensi_jadwal_asisten')
            ->where('tingkat_preferensi', 'Bisa')
            ->update(['tingkat_preferensi' => 'Bersedia']);

        DB::table('preferensi_jadwal_asisten')
            ->where('tingkat_preferensi', 'Sangat Suka')
            ->update(['tingkat_preferensi' => 'Prioritas Utama']);

        DB::table('preferensi_jadwal_asisten')
            ->where('tingkat_preferensi', 'Kurang Suka')
            ->update(['tingkat_preferensi' => 'Cadangan']);
            
        DB::table('preferensi_jadwal_asisten')
            ->where('tingkat_preferensi', 'Tidak Bisa')
            ->update(['tingkat_preferensi' => 'Tidak Bersedia']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
