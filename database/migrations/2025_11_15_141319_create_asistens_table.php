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
        Schema::create('asistens', function (Blueprint $table) {
            $table->id('id_asisten');
            $table->string('nama', 100);
            $table->string('npm', 20)->unique();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('no_telepon', 20)->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamp('tanggal_daftar')->useCurrent();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistens');
    }
};
