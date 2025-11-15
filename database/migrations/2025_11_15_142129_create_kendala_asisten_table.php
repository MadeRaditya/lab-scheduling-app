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
        Schema::create('kendala_asisten', function (Blueprint $table) {
            $table->id('id_kendala');
            $table->foreignId('id_asisten')->constrained('asistens')->onDelete('cascade');
            $table->foreignId('id_semester')->constrained('semesters')->onDelete('cascade');
            $table->string('tipe_kendala', 50)->default('Lainnya');
            $table->string('hari', 20)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('alasan');
            $table->string('status', 20)->default('Aktif');
            $table->unique(['id_asisten', 'id_semester', 'hari']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendala_asisten');
    }
};
