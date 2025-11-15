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
        Schema::create('jadwal_labs', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->foreignId('id_semester')->constrained('semesters', 'id_semester')->onDelete('restrict');
            $table->string('kode_mk', 10);
            $table->foreignId('id_ruangan')->constrained('ruangans', 'id_ruangan')->onDelete('restrict');
            $table->string('hari', 20);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('jumlah_mahasiswa');
            $table->integer('jumlah_asisten_dibutuhkan')->default(1);
            $table->string('prioritas', 20)->default('Sedang');
            $table->string('status', 30)->default('Draft');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->foreign('kode_mk')->references('kode_mk')->on('matakuliahs')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_labs');
    }
};
