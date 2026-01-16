<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("matakuliahs", function (Blueprint $table) {
            $table->id();
            $table->string("kode_mk", 10);
            $table->string("nama_mk", 100);
            $table->integer("sks");
            $table->string("kelas", 10)->nullable();
            $table->string("dosen", 100)->nullable();
            $table->integer("semester_berjalan")->default(1);
            $table->text("deskripsi")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("matakuliahs");
    }
};
