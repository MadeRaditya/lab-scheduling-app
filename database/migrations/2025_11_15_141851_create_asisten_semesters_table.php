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
        Schema::create('asisten_semesters', function (Blueprint $table) {
            $table->id('id_asisten_semester');
            $table->foreignId('id_asisten')
                ->constrained('asistens', 'id_asisten')
                ->onDelete('cascade');
            $table->foreignId('id_semester')
                ->constrained('semesters', 'id_semester')
                ->onDelete('cascade');
            $table->string('status', 20)->default('Aktif');
            $table->string('peran', 20)->default('Asisten');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->unique(['id_asisten', 'id_semester']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asisten_semesters');
    }
};
