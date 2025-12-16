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
        Schema::create('asisten_matakuliah', function (Blueprint $table) {
            $table->foreignId('id_asisten')->constrained('asistens', 'id_asisten')->onDelete('cascade');
            $table->string('kode_mk', 10);
            $table->primary(['id_asisten', 'kode_mk']);
            $table->timestamps();

            $table->foreign('kode_mk')->references('kode_mk')->on('matakuliahs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asisten_matakuliah');
    }
};
