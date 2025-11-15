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
        Schema::create('kompetensis', function (Blueprint $table) {
            $table->id('id_kompetensi');
            $table->string('nama_kompetensi', 100);
            $table->text('deskripsi')->nullable();
            $table->string('kategori', 50);
            $table->string('level', 20)->default('Dasar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kompetensis');
    }
};
