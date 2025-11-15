<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penugasan_asisten_finals', function (Blueprint $table) {
            $table->id('id_penugasan');
            $table->foreignId('id_jadwal_final')->constrained('jadwal_finals', 'id_final')->onDelete('cascade');
            $table->foreignId('id_asisten')->constrained('asistens', 'id_asisten')->onDelete('restrict');
            $table->foreignId('id_kompetensi')->nullable()->constrained('kompetensis', 'id_kompetensi')->onDelete('set null');
            $table->decimal('skor_kecocokan', 3, 2)->default(1.00);
            $table->string('peran', 50)->default('Asisten Utama');
            $table->string('status_kehadiran', 30)->default('Terjadwal');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasan_asisten_finals');
    }
};
