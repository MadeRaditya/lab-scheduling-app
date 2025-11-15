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
        Schema::create('ruangans', function (Blueprint $table) {
            $table->id('id_ruangan');
            $table->string('nama_ruangan', 100);
            $table->string('gedung', 50)->nullable();
            $table->string('lantai', 20)->nullable();
            $table->integer('kapasitas')->default(30);
            $table->string('kategori', 100)->nullable();
            $table->string('status', 20)->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};
