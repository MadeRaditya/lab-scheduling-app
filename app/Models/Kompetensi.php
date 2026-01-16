<?php

namespace App\Models;

use App\Enums\KategoriKompetensi;
use App\Enums\LevelKompetensi;
use App\Enums\PreferensiTingkat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kompetensi extends Model
{
    use HasFactory;
    protected $table = "kompetensis";
    protected $primaryKey = "id_kompetensi";
    public $timestamps = true;
    protected $fillable = ["nama_kompetensi", "deskripsi", "kategori", "level"];
    protected $casts = [
        "kategori" => KategoriKompetensi::class,
        "level" => LevelKompetensi::class,
    ];
    public function asistens(): BelongsToMany
    {
        return $this->belongsToMany(
            Asisten::class,
            "asisten_kompetensi",
            "id_kompetensi",
            "id_asisten",
        )
            ->withPivot([
                "tingkat_penguasaan",
                "tanggal_sertifikasi",
                "sertifikasi",
            ])
            ->withTimestamps();
    }
    public function matakuliahs(): BelongsToMany
    {
        return $this->belongsToMany(
            Matakuliah::class,
            "matakuliah_kompetensi",
            "id_kompetensi",
            "matakuliah_id",
        )
            ->using(MatakuliahKompetensi::class)
            ->withPivot(["tingkat_dibutuhkan", "prioritas"])
            ->withTimestamps();
    }
}
