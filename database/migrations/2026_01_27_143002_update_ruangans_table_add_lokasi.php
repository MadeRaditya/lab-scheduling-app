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
        Schema::table('ruangans', function (Blueprint $table) {
            $table->string('lokasi', 20)->nullable()->after('nama_ruangan');

            $table->dropColumn(['gedung', 'lantai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->string('gedung', 50)->nullable();
            $table->integer('lantai')->nullable();
            $table->dropColumn('lokasi');
        });
    }
};
