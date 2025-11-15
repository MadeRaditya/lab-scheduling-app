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
        Schema::create('asisten_kompetensi', function (Blueprint $table) {
            $table->foreignId('id_asisten')->constrained('asistens', 'id_asisten')->onDelete('cascade');
            $table->foreignId('id_kompetensi')->constrained('kompetensis')->onDelete('cascade');
            $table->string('tingkat_penguasaan', 20)->default('Menengah');
            $table->date('tanggal_sertifikasi')->nullable();
            $table->string('sertifikasi', 255)->nullable();
            $table->primary(['id_asisten', 'id_kompetensi']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asisten_kompetensi');
    }
};
