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
        Schema::create("asisten_matakuliah", function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("id_asisten");
            $table
                ->foreign("id_asisten")
                ->references("id_asisten")
                ->on("asistens")
                ->onDelete("cascade");

            $table
                ->foreignId("matakuliah_id")
                ->constrained("matakuliahs")
                ->onDelete("cascade");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("asisten_matakuliah");
    }
};
