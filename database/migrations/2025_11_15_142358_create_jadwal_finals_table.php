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
        Schema::create("jadwal_finals", function (Blueprint $table) {
            $table->id("id_final");
            $table
                ->foreignId("id_semester")
                ->constrained("semesters", "id_semester")
                ->onDelete("restrict");
            $table
                ->foreignId("matakuliah_id")
                ->constrained("matakuliahs")
                ->onDelete("cascade");
            $table
                ->foreignId("id_ruangan")
                ->constrained("ruangans", "id_ruangan")
                ->onDelete("restrict");
            $table->string("hari", 20);
            $table->time("jam_mulai");
            $table->time("jam_selesai");
            $table->timestamp("tanggal_dibuat")->useCurrent();
            $table
                ->string("metode_penjadwalan", 50)
                ->default("Greedy Algorithm");
            $table->string("status", 20)->default("Aktif");
            $table->text("catatan")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("jadwal_finals");
    }
};
