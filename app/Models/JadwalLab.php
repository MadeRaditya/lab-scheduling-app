<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalLab extends Model
{
    use HasFactory;
    protected $table = "jadwal_labs";
    protected $primaryKey = "id_jadwal";
    public $timestamps = true;
    protected $fillable = [
        "id_semester",
        "matakuliah_id",
        "id_ruangan",
        "hari",
        "jam_mulai",
        "jam_selesai",
        "jumlah_mahasiswa",
        "jumlah_asisten_dibutuhkan",
        "prioritas",
        "status",
        "keterangan",
    ];

    protected $casts = [
        "jam_mulai" => "string",
        "jam_selesai" => "string",
    ];
    // calculate jumlah_asisten_dibutuhkan on creating/updating in model to avoid double triggers
    protected static function booted()
    {
        static::saving(function ($model) {
            $model->jumlah_asisten_dibutuhkan =
                (int) ($model->jumlah_mahasiswa >= 20 ? 2 : 1);
        });
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, "id_semester");
    }
    public function matakuliah(): BelongsTo
    {
        return $this->belongsTo(Matakuliah::class, "matakuliah_id");
    }
    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, "id_ruangan");
    }
}
