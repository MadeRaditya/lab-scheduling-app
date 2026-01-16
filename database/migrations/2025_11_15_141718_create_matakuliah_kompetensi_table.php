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
        Schema::create("matakuliah_kompetensi", function (Blueprint $table) {
            $table
                ->foreignId("matakuliah_id")
                ->constrained("matakuliahs")
                ->onDelete("cascade");

            $table
                ->foreignId("id_kompetensi")
                ->constrained("kompetensis", "id_kompetensi")
                ->onDelete("cascade");

            $table->string("tingkat_dibutuhkan", 20)->default("Dasar");
            $table
                ->integer("prioritas")
                ->default(1)
                ->comment("1: Wajib, 2: Disarankan, 3: Opsional");

            $table->primary(["matakuliah_id", "id_kompetensi"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("matakuliah_kompetensi");
    }
};
