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
        Schema::create('matakuliah_kompetensi', function (Blueprint $table) {
            $table->string('kode_mk', 10);
            $table->foreignId('id_kompetensi')->constrained('kompetensis')->onDelete('cascade');
            $table->string('tingkat_dibutuhkan', 20)->default('Dasar');
            $table->integer('prioritas')->default(1)->comment('1: Wajib, 2:Disarankan, 3: Opsional');
            $table->primary(['kode_mk', 'id_kompetensi']);
            $table->timestamps();
            $table->foreign('kode_mk')->references('kode_mk')->on('matakuliahs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matakuliah_kompetensi');
    }
};
