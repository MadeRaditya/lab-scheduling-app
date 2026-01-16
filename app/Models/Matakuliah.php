<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matakuliah extends Model
{
    use HasFactory;
    protected $table = "matakuliahs";

    public $timestamps = true;
    protected $fillable = [
        "kode_mk",
        "nama_mk",
        "sks",
        "kelas",
        "dosen",
        "semester_berjalan",
        "deskripsi",
    ];
    public function kompetensis(): BelongsToMany
    {
        return $this->belongsToMany(
            Kompetensi::class,
            "matakuliah_kompetensi",
            "matakuliah_id",
            "id_kompetensi",
        )
            ->using(MatakuliahKompetensi::class)
            ->withPivot(["tingkat_dibutuhkan", "prioritas"])
            ->withTimestamps();
    }
    public function jadwalLabs(): HasMany
    {
        return $this->hasMany(JadwalLab::class, "matakuliah_id");
    }
    public function jadwalFinals(): HasMany
    {
        return $this->hasMany(JadwalFinal::class, "matakuliah_id");
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, "semester_berjalan");
    }

    public function asistens(): BelongsToMany
    {
        return $this->belongsToMany(
            Asisten::class,
            "asisten_matakuliah",
            "matakuliah_id",
            "id_asisten",
        )->withTimestamps();
    }
}
