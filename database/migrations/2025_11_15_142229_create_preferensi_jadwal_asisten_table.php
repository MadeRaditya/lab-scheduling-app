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
        Schema::create('preferensi_jadwal_asisten', function (Blueprint $table) {
            $table->id('id_preferensi');
            $table->foreignId('id_asisten')->constrained('asistens', 'id_asisten')->onDelete('cascade');
            $table->foreignId('id_semester')->constrained('semesters', 'id_semester')->onDelete('cascade');
            $table->string('hari', 20);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('tingkat_preferensi', 30)->default('Bisa');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferensi_jadwal_asisten');
    }
};
