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
        Schema::create('jadwal_asisten', function (Blueprint $table) {
            $table->id('id_jadwal_asisten');
            $table->foreignId('id_asisten')->constrained('asistens', 'id_asisten')->onDelete('cascade');
            $table->foreignId('id_semester')->constrained('semesters', 'id_semester')->onDelete('cascade');
            $table->string('hari', 20);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('keterangan', 255)->nullable();
            $table->string('jenis_kegiatan', 50)->default('Kuliah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_asisten');
    }
};
