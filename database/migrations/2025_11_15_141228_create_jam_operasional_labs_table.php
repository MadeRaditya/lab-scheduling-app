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
        Schema::create('jam_operasional_labs', function (Blueprint $table) {
            $table->id('id_jam_op');
            $table->string('hari', 20)->unique();
            $table->time('jam_buka');
            $table->time('jam_tutup');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_operasional_labs');
    }
};
